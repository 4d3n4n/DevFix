
function updateImgDisplay() {
    while (previewImg.firstChild) {
        previewImg.removeChild(previewImg.firstChild);
    }

    const currentFiles = inputImg.files;

    for (const file of currentFiles) {
        const imgPreview = document.createElement('img');
        if (!validFileType(file)) {
            return false;
        }
        imgPreview.src = URL.createObjectURL(file);
        imgPreview.alt = file.name;
        previewImg.append(imgPreview);
    }
}


const addModelForm = document.querySelector('.add-modele-form');
const addButton = document.querySelector('.container-cta');

addButton.addEventListener('click', () => {
    if (addModelForm.style.visibility === "visible") {
        addModelForm.style.visibility = "hidden";
        addModelForm.style.opacity = '0';
        addModelForm.style.padding = '0';
        addModelForm.style.width = '0';
        addModelForm.style.margin = '0';
        addModelForm.style.position = 'absolute';
        addModelForm.style.height = '0';
        return;
    } else {
        addModelForm.style.visibility = "visible";
        addModelForm.style.opacity = '1';
        addModelForm.style.width = '40vw';
        addModelForm.style.margin = '1.25rem 1.875rem 1.875rem 1.25rem';
        addModelForm.style.padding = '50px';
        addModelForm.style.position = 'relative';
        addModelForm.style.height = 'auto';
    }
});



const editForm = document.querySelector('.edit-modele-form-container');
const modeleCards = document.querySelectorAll('.modele-card');
const closeEditForm = editForm.querySelector('.btn-cancel');

modeleCards.forEach(card => {
    card.addEventListener('click', () => {
        editForm.classList.remove('hide-pop-up');
    });
});

closeEditForm.addEventListener('click', () => {
    editForm.classList.add('hide-pop-up');
});