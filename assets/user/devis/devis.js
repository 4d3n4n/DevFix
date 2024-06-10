const logIn = document.getElementById('log-in-btn');
const signInForm = document.querySelector('.sign-in-form');
const signIn = document.getElementById('sign-in-btn');
const logInForm = document.querySelector('.log-in-form');


logIn.addEventListener('click', () => {
    signInForm.classList.add('hide-form');
    logInForm.classList.remove('hide-form');
});

signIn.addEventListener('click', () => {
    logInForm.classList.add('hide-form');
    signInForm.classList.remove('hide-form');
});



const conditionPopUpButton = document.querySelectorAll('.condition-pop-up-button');
const conditionPopUp = document.querySelector('.condition-pop-up-container');

conditionPopUpButton.forEach(button => {
    button.addEventListener('click', () => {
        conditionPopUp.classList.remove('hide-pop-up');
    });
});

const closeConditionPopUp = document.querySelector('.close-icon-container');

closeConditionPopUp.addEventListener('click', () => {
    conditionPopUp.classList.add('hide-pop-up');
});




// MAJ TOTALE
const devisForms = document.querySelectorAll('form');

devisForms.forEach(noLoggedForm => {
    let checkboxes = noLoggedForm.querySelectorAll('input[type="checkbox"][name="tarifIds[]"]');
    function updateTotal() {
        let total = 0;
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                let montantText = checkbox.nextElementSibling.textContent;
                let montant = parseFloat(montantText.match(/\d+(,\d+)?/)[0].replace(',', '.'));
                total += montant;
            }
        });
        document.getElementById('total').textContent = total.toFixed(2).replace('.', ',') + ' €';
    }
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateTotal);
    });
    updateTotal();
});




// PREVIENT L'ENVOI DU FORMULAIRE SI AUCUNE PANNE N'EST SELECTIONNEE
const noLoggedSection = document.querySelector('.no-logged-in-form');
noLoggedSection.addEventListener('submit', function(e) {
    const checkboxes = noLoggedSection.querySelectorAll('input[type="checkbox"][name="tarifIds[]"]');
    const isAnyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    if (!isAnyChecked) {
        e.preventDefault();
        alert('Veuillez sélectionner au moins une panne.');
    }
});




// VALIDATION ET DÉSACTIVATION DU BOUTON LOG IN
const noLoggedInForm = document.querySelector('.no-logged-in-form');
const forms = noLoggedInForm.querySelectorAll('form');

forms.forEach(form => {
    function validateForm() {
        const button = form.querySelector('button[type="submit"]');
        
        if (form.id === 'inscriptionForm') {
            const conditionCheckBox = form.querySelector('.form-check-input'); // condition generales
            const inputsPrice = form.querySelectorAll('input[type="checkbox"].price-input'); // checkbox prix devis
            const inputs = form.querySelectorAll('input[type="text"]:not(#inputAddress2):not(#code_imei_log_in):not(#num_serie_log_in):not(#code_imei_sign_in):not(#num_serie_sign_in), input[type="number"], input[type="email"], input[type="password"]');
        
            const areInputsFilled = Array.from(inputs).every(input => input.value.trim() !== '');
            const isConditionChecked = conditionCheckBox.checked;
            const isAtLeastOneChecked = Array.from(inputsPrice).some(input => input.checked);
        
            button.disabled = !areInputsFilled || !isConditionChecked || !isAtLeastOneChecked;
        } else {
            const inputs = form.querySelectorAll('input[type="email"], input[type="password"]');
            const areInputsFilled = Array.from(inputs).every(input => input.value.trim() !== '');
            
            button.disabled = !areInputsFilled;
        }
    }    

    const allInputs = form.querySelectorAll('input');
    allInputs.forEach(input => {
        input.addEventListener('change', validateForm);
        input.addEventListener('input', validateForm);
    });

    validateForm();
});
