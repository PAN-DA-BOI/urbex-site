document.addEventListener('DOMContentLoaded', function() {
    const chatBox = document.getElementById('chat-box');
    const chatInput = document.getElementById('chat-input');
    const sendButton = document.getElementById('send-button');
    const socket = io();

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
        socket.emit('chat message', message);
    }

    socket.on('chat message', function(message) {
        displayMessage(message);
    });

    function displayMessage(message) {
        const chatMessage = document.createElement('div');
        chatMessage.classList.add('chat-message');
        chatMessage.textContent = `${message.user} (${new Date(message.timestamp).toLocaleTimeString()}) : ${message.content}`;
        chatBox.appendChild(chatMessage);
        chatBox.scrollTop = chatBox.scrollHeight; // Scroll to the bottom
    }
});
