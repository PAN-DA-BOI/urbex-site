document.addEventListener('DOMContentLoaded', function() {
    const chatBox = document.getElementById('chat-box');
    const chatInput = document.getElementById('chat-input');
    const sendButton = document.getElementById('send-button');
    const socket = new WebSocket('ws://localhost:8080'); // Change to your server URL

    socket.addEventListener('open', function() {
        console.log('Connected to the server');
    });

    socket.addEventListener('message', function(event) {
        const message = JSON.parse(event.data);
        displayMessage(message);
    });

    sendButton.addEventListener('click', function() {
        const message = chatInput.value;
        if (message.trim() !== '') {
            sendMessage(message);
            chatInput.value = '';
        }
    });

    chatInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            sendButton.click();
        }
    });

    function sendMessage(message) {
        const messageData = {
            user: 'User', // Replace with actual user info
            content: message,
            timestamp: new Date().toISOString()
        };
        socket.send(JSON.stringify(messageData));
    }

    function displayMessage(message) {
        const chatMessage = document.createElement('div');
        chatMessage.classList.add('chat-message');
        chatMessage.textContent = `${message.user} (${new Date(message.timestamp).toLocaleTimeString()}) : ${message.content}`;
        chatBox.appendChild(chatMessage);
        chatBox.scrollTop = chatBox.scrollHeight; // Scroll to the bottom
    }
});
