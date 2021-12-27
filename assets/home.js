const form = document.querySelector("#shrotenform");
const shortenCard = document.querySelector("#shortenCard");
const inputURL = document.querySelector("#url");
const shortenBtn = document.querySelector("#btnShortenUrl");

const URL_SHORTEN = "/ajax/shorten";

const errorMessages = {
    'Missing args': 'Veuillez fournir une url valide.',
    'Invalid arg URl': 'Impossible de raccourcir ce lien, Ce n\'est pas une url valide.',
};

form.addEventListener("submit", (e) => {
    e.preventDefault();
    fetch(URL_SHORTEN, {
        method: 'POST',
        body: new FormData(e.target)
    })
        .then(response => response.json())
        .then(handleData);
});

const handleData = function (data) {
    if (data.statusCode >= 400) {
        return handleError(data);
    }

    inputURL.value = data.link;
    shortenBtn.innerText = "Copier";
    shortenBtn.addEventListener("click", (e) => {
        e.preventDefault();
        inputURL.select();
        document.execCommand('copy');

        this.innerText = "Réduire l'Url"
    }, {once: true});
};

const handleError = function (data) {
    const alert = document.createElement("div");
    alert.classList.add('alert', 'alert-danger', 'mt-2');
    alert.innerText = errorMessages[data.statusText];

    shortenCard.after(alert);
}