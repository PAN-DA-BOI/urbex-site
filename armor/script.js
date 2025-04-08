let packet = "00000";

function goBack() {
    location.reload();
}

function createButton(text, imageUrl, onClick) {
    const button = document.createElement('div');
    button.className = 'button';
    button.style.backgroundImage = `url(${imageUrl})`;
    button.innerHTML = `<span>${text}</span>`;
    button.onclick = onClick;
    return button;
}

function initializeGUI() {
    const gui = document.getElementById('gui');
    gui.innerHTML = '';

    const basicButton = createButton('Basic', 'basic.png', () => {
        window.location.href = 'basic.zip';
    });

    const customizeButton = createButton('Customize', 'customize.png', showCustomizeOptions);

    gui.appendChild(basicButton);
    gui.appendChild(customizeButton);
}

function showCustomizeOptions() {
    const gui = document.getElementById('gui');
    gui.innerHTML = '';

    const options = ['Bat', 'Porcupine', 'Owl', 'Eel'];
    const images = [
        'path/to/bat.png',
        'path/to/porcupine.png',
        'path/to/owl.png',
        'path/to/eel.png'
    ];

    options.forEach((option, index) => {
        const button = createButton(option, images[index], () => {
            updatePacket(index, option[0]);
        });
        gui.appendChild(button);
    });

    const submitButton = document.createElement('button');
    submitButton.className = 'submit-button';
    submitButton.innerText = 'Submit';
    submitButton.onclick = () => {
        sendNotification(packet);
    };
    gui.appendChild(submitButton);
}

function updatePacket(index, value) {
    packet = packet.split('');
    packet[index] = value;
    packet = packet.join('');
}

async function sendNotification(packet) {
    try {
        const response = await fetch('http://localhost:3000/send-notification', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ packet }),
        });

        if (response.ok) {
            alert('Notification sent successfully');
        } else {
            alert('Failed to send notification');
        }
    } catch (error) {
        console.error('Error sending notification:', error);
    }
}

initializeGUI();
