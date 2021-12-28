import {Toast} from "bootstrap";

const copyToast = new Toast(document.querySelector('#copyToast'), {
    autohide: true,
    delay: 2000,
})

const listGroupItems = document.querySelectorAll(".list-group-item");
const actions = document.querySelector("#actions");


const btnCopy = document.querySelector("#btnCopy");
const btnDelete = document.querySelector("#btnDelete");
const btnStats = document.querySelector("#btnStats");

const URL_DELETE = '/ajax/delete';
const URL_STATISTICS = '/statistics'

let hash = null;
let selectedItem = null;

listGroupItems.forEach((item) => {
    item.addEventListener('click', () => {
        if (selectedItem === item) {
            selectedItem = null;
            hash = null;
            item.classList.remove('active');
            toggleButtonsInteraction(true);
            return;
        }
        listGroupItems.forEach((item) => item.classList.remove('active'));
        selectedItem = item;
        selectedItem.classList.add('active');
        hash = selectedItem.dataset.hash;
        toggleButtonsInteraction();
    });
});

btnCopy.addEventListener('click', evt => {
    const link = document.querySelector(`#anchor_${hash}`);
    navigator.clipboard.writeText(link.href).then(() => {
        copyToast.show();
    });
});

btnStats.addEventListener('click', () => {
   if(hash){
       window.open(`${URL_STATISTICS}/${hash}`);
   }
});

btnDelete.addEventListener('click', () => {
    fetch(`${URL_DELETE}/${hash}`).then((response) => response.json()).then(handleData);
});

const toggleButtonsInteraction = function (isDisabled = false) {
    Array.from(actions.children).forEach((button) => {
        button.disabled = isDisabled;
    })
}

const handleData = (data) => {
    switch (data.statusCode){
        case 'DELETE_SUCCESSFUL':
            selectedItem.remove();
            break;
        case 'URL_NOT_FOUND':
            break;
    }
};