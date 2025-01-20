const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const bcrypt = require('bcrypt');
const session = require('express-session');
const bodyParser = require('body-parser');
const fs = require('fs');
const path = require('path');

const app = express();
const server = http.createServer(app);
const io = socketIo(server);

app.use(bodyParser.json());
app.use(session({
    secret: 'your-secret-key',
    resave: false,
    saveUninitialized: true
}));

// Serve static files
app.use(express.static(path.join(__dirname, '..')));

// In-memory storage for users, votes, and chat logs
let users = [];
let votes = { option1: 0, option2: 0, option3: 0 };
let voteLogs = [];
let chatLogs = [];
let countdownTimer = 60; // 60 seconds for demonstration purposes
let timerInterval;

// Authentication middleware
const authMiddleware = (req, res, next) => {
    if (req.session.user) {
        next();
    } else {
        res.status(401).send('Unauthorized');
    }
};

// Login endpoint
app.post('/login', (req, res) => {
    const { username, password } = req.body;
    const user = users.find(u => u.username === username);
    if (user && bcrypt.compareSync(password, user.password)) {
        req.session.user = user;
        res.status(200).send('Login successful');
    } else {
        res.status(401).send('Invalid credentials');
    }
});

// Register endpoint
app.post('/register', (req, res) => {
    const { username, password } = req.body;
    const hashedPassword = bcrypt.hashSync(password, 10);
    users.push({ username, password: hashedPassword });
    res.status(200).send('Registration successful');
});

// Update user settings endpoint
app.post('/settings', authMiddleware, (req, res) => {
    const { username, password } = req.body;
    const user = users.find(u => u.username === req.session.user.username);
    if (user) {
        user.username = username;
        user.password = bcrypt.hashSync(password, 10);
        req.session.user = user;
        res.status(200).send('Settings updated successfully');
    } else {
        res.status(404).send('User not found');
    }
});

// Vote endpoint
app.post('/vote', authMiddleware, (req, res) => {
    const { option } = req.body;
    if (votes[option] !== undefined) {
        votes[option]++;
        voteLogs.push({ user: req.session.user.username, option });
        res.status(200).send('Vote recorded');
    } else {
        res.status(400).send('Invalid option');
    }
});

// Countdown and vote declaration
const startCountdown = () => {
    timerInterval = setInterval(() => {
        countdownTimer--;
        if (countdownTimer <= 0) {
            clearInterval(timerInterval);
            declareWinner();
        }
        io.emit('countdown', countdownTimer);
    }, 1000);
};

const declareWinner = () => {
    const maxVotes = Math.max(...Object.values(votes));
    const winners = Object.keys(votes).filter(option => votes[option] === maxVotes);
    const winner = winners[Math.floor(Math.random() * winners.length)];
    io.emit('winner', winner);
    freezeChatLogs();
    resetVotes();
};

const freezeChatLogs = () => {
    const timestamp = new Date().toISOString();
    fs.writeFileSync(path.join(__dirname, 'chat-logs', `${timestamp}.json`), JSON.stringify(chatLogs));
    chatLogs = [];
};

const resetVotes = () => {
    votes = { option1: 0, option2: 0, option3: 0 };
    voteLogs = [];
    countdownTimer = 60;
    startCountdown();
};

// Socket.io events
io.on('connection', (socket) => {
    socket.on('chat message', (msg) => {
        const message = { user: socket.request.session.user.username, content: msg, timestamp: new Date().toISOString() };
        chatLogs.push(message);
        io.emit('chat message', message);
    });

    socket.on('disconnect', () => {
        console.log('User disconnected');
    });
});

// Start the server
const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
    startCountdown();
});
