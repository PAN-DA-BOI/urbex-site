document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.custom-button');

    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            button.style.transition = 'background 1.5s';
        });

        button.addEventListener('mouseleave', function() {
            button.style.transition = 'background 1.5s';
        });
    });
});
