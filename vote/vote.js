document.addEventListener('DOMContentLoaded', function() {
    const voteButtons = document.querySelectorAll('.vote-button');
    const votes = {};
    let votedOption = null;

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
});
