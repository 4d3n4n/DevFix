// VALIDATION ET DÉSACTIVATION DU BOUTON LOG IN
const form = document.querySelector('form');

function validateForm() {
    const inputs = form.querySelectorAll('input[type="text"], input[type="number"], input[type="email"], input[type="password"]');
    const button = form.querySelector('button[type="submit"]');

    const areInputsFilled = Array.from(inputs).every(input => input.value.trim() !== '');

    button.disabled = !areInputsFilled;
}

const allInputs = document.querySelectorAll('input[type="text"], input[type="number"], input[type="email"], input[type="password"]');
allInputs.forEach(input => {
    input.addEventListener('change', validateForm);
    input.addEventListener('input', validateForm);
});

validateForm();