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
let checkboxes = document.querySelectorAll('input[type="checkbox"][name="tarifIds[]"]');
function updateTotal() {
    let total = 0;
    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
            let montant = checkbox.nextElementSibling.innerText.split(': ')[1].replace(' €', '').replace(',', '.');
            total += parseFloat(montant);
        }
    });
    document.getElementById('total').textContent = total.toFixed(2).replace('.', ',') + ' €';
}
checkboxes.forEach(function(checkbox) {
    checkbox.addEventListener('change', updateTotal);
});
updateTotal();



// PREVIENT L'ENVOI DU FORMULAIRE SI AUCUNE PANNE N'EST SELECTIONNEE
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
    const checkboxes = form.querySelectorAll('input[type="checkbox"][name="tarifIds[]"]');
    const isAnyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    if (!isAnyChecked) {
        e.preventDefault();
        alert('Veuillez sélectionner au moins une panne.');
    }
});


// VALIDATION ET DÉSACTIVATION DU BOUTON
const conditionLoggedInCheckBox = document.querySelector('.logged-in-checkbox');
const submitLogInButton = document.querySelector('#confirmation-btn');

function validateFormAndToggleSubmitButton() {
    const isAnyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
    
    const inputs = document.querySelectorAll('input[type="text"]:not(#code_imei):not(#num_serie), input[type="email"], input[type="password"]');
    const areInputsFilled = Array.from(inputs).every(input => input.value.trim() !== '');
    
    const isConditionChecked = conditionLoggedInCheckBox.checked;
    
    submitLogInButton.disabled = !isAnyChecked || !areInputsFilled || !isConditionChecked;
}

document.querySelectorAll('input[type="checkbox"][name="tarifIds[]"], input[type="text"], input[type="email"], input[type="password"], .logged-in-checkbox')
.forEach(input => {
    input.addEventListener('change', validateFormAndToggleSubmitButton);
    input.addEventListener('input', validateFormAndToggleSubmitButton);
});

validateFormAndToggleSubmitButton();
