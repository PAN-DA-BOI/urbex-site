document.addEventListener('DOMContentLoaded', function() {
    const voteButtons = document.querySelectorAll('.vote-button');
    const votes = {};
    let votedOption = null;
    let countdown;

    // Initialize votes
    ['option1', 'option2', 'option3'].forEach(option => {
        votes[option] = 0;
    });

    // Set up countdown timer
    const countdownElement = document.getElementById('countdown');
    const countdownDuration = 60; // 60 seconds for demonstration purposes
    let timeRemaining = countdownDuration;

    function updateCountdown() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        countdownElement.textContent = `Time Remaining: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        if (timeRemaining <= 0) {
            clearInterval(countdown);
            declareWinner();
        } else {
            timeRemaining--;
        }
    }

    function startCountdown() {
        updateCountdown();
        countdown = setInterval(updateCountdown, 1000);
    }

    function declareWinner() {
        const maxVotes = Math.max(...Object.values(votes));
        const winners = Object.keys(votes).filter(option => votes[option] === maxVotes);
        const winner = winners[Math.floor(Math.random() * winners.length)];
        window.location.href = `win.html?winner=${winner}`;
    }

    voteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const option = button.dataset.option;
            if (!votes[option]) {
                votes[option] = 0;
            }
            votes[option]++;
            console.log(`Voted for ${option}. Current votes:`, votes);

            // Change the button color to indicate it has been voted
            if (votedOption) {
                document.querySelector(`.vote-button[data-option="${votedOption}"]`).classList.remove('voted');
            }
            button.classList.add('voted');
            votedOption = option;

            // Here you can add code to send the vote to the server or update the UI
        });
    });

    startCountdown();
});
