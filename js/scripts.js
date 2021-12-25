/* Header -------------------------------------------- */

function toggleAccountDropdown() {
    document.getElementById('accountDropdown').classList.toggle('dropdown');
    document.getElementById('accountSection').classList.remove('slideOut');
    document.getElementById('loginSection').classList.remove('slideIn');
    document.getElementById('signupSection').classList.remove('slideIn');
}

function openLogin() {
    document.getElementById('accountSection').classList.toggle('slideOut');
    document.getElementById('loginSection').classList.toggle('slideIn');
}

function openSignup() {
    document.getElementById('accountSection').classList.toggle('slideOut');
    document.getElementById('signupSection').classList.toggle('slideIn');
}

function loginBack() {
    document.getElementById('accountSection').classList.toggle('slideOut');
    document.getElementById('loginSection').classList.toggle('slideIn');
}

function signupBack() {
    document.getElementById('accountSection').classList.toggle('slideOut');
    document.getElementById('signupSection').classList.toggle('slideIn');
}

/* Lista Film ---------------------------------------- */

function listaFilmLike(checked, id) {
    elem = document.getElementById('label-' + id);
    if (checked) {
        elem.innerHTML = elem.innerHTML.replace('favorite_border', 'favorite');
    } else {
        elem.innerHTML = elem.innerHTML.replace('favorite', 'favorite_border');
    }
}

/* Acquisto biglietti -------------------------------- */

function acquistoBigliettiLogin() {
    document.getElementById('contentLogin').classList.toggle('show');
    document.getElementById('contentSignin').classList.remove('show');
}

function acquistoBigliettiSignup() {
    document.getElementById('contentSignin').classList.toggle('show');
    document.getElementById('contentLogin').classList.remove('show');
}

function paymentSelectionChanged() {
    cdc_st = document.getElementById('carta_di_credito').checked;
    lcdc = document.getElementById('label-carta_di_credito');
    if (cdc_st) {
        if (!lcdc.classList.contains('selected')) lcdc.classList.add('selected');
    } else lcdc.classList.remove('selected');

    sastispay_st = document.getElementById('satispay').checked;
    lsatispay = document.getElementById('label-satispay');
    if (sastispay_st) {
        if (!lsatispay.classList.contains('selected')) lsatispay.classList.add('selected');
    } else lsatispay.classList.remove('selected');

    paypal_st = document.getElementById('paypal').checked;
    lpaypal = document.getElementById('label-paypal');
    if (paypal_st) {
        if (!lpaypal.classList.contains('selected')) lpaypal.classList.add('selected');
    } else lpaypal.classList.remove('selected');
}

/* Pagina Utente ----------------------------------- */

function showHideButton(id){
    var x = document.getElementById(id);
    if (x.style.display === "none") {
        x.style.display = "inline";
    } else {
        x.style.display = "none";
    }
}

function setEditOn() {
    showHideButton('buttonInvia');
    showHideButton('buttonReset');
    showHideButton('buttonAnnulla');
    showHideButton('buttonModifica');
    document.getElementById('nome').removeAttribute('disabled');
    document.getElementById('cognome').removeAttribute('disabled');
    document.getElementById('dataNascita').removeAttribute('disabled');
    document.getElementById('email').removeAttribute('disabled');
    document.getElementById('password').removeAttribute('disabled');
}

function setEditOff() {
    showHideButton('buttonInvia');
    showHideButton('buttonReset');
    showHideButton('buttonAnnulla');
    showHideButton('buttonModifica');
    document.getElementById('infoUtenteForm').reset();
    document.getElementById('nome').setAttribute('disabled', '');
    document.getElementById('cognome').setAttribute('disabled', '');
    document.getElementById('dataNascita').setAttribute('disabled', '');
    document.getElementById('email').setAttribute('disabled', '');
    document.getElementById('password').setAttribute('disabled', '');
}

/*
		chiave: campo input di cui inserisco le informazioni
		[0]: placeholder (indicazioni prima della digitazione)
		[1]: espressione regolare
		[2]: messaggio di errore
*/
function getInfoutenteFormDetails() {
    return {
        "nome": ["Nome del personaggio, almeno due caratteri", /^[A-Za-zaeriou\s]{2,}$/, "inserire un nome di lunghezza almeno due"],
        "colore": ["Colore del personaggio", /^[A-Za-zaeriou\s]{2,}$/, "inserire un colore di soli caratteri di lunghezza almeno due"],
        "peso": ["Peso del personaggio", /^[0-9]+$/, "inserire unnumero"],
        "descrizione": ["Descrizione del personaggio", /^.{10,}$/, "la descrizione deve essere lunga almeono dieci caratteri"]
    };
}

function mostraErrore(input) {
    var parent = input.parentNode;
    var error = document.createElement("strong");
    //error.className = "errorSuggestion"; //classe css da fare
    error.appendChild(document.createTextNode(getInfoutenteFormDetails()[input.id][2]));
    parent.appendChild(error);
}

function caricamento(details) {
    for (var key in details) {
        var input = document.getElementById(key);
        campoDefault(input);
        input.onblur = function () {
            validazioneCampo(this);
        };
    }
}

function campoDefault(input) {
    if (input.value == "") {
        input.setAttribute('placeholder', getInfoutenteFormDetails()[input.id][0]);
    }
}


function validazioneCampo(input, details) {
    var parent = nome.parentNode;
    if (parent.children.length == 2) {
        parent.removeChild(parent.children[1]);
    }

    var regex = details[input.id][1];
    var text = input.value;

    if (text.search(regex) !== 0 || (text == getInfoutenteFormDetails()[input.id][0])) {
        mostraErrore(input);
        input.focus();
        input.select();
        return false;
    }
    return true;
}

function validateForm() {
    var details = getInfoutenteFormDetails();
    for (var key in details) {
        var input = document.getElementById(key);
        if (!validazioneCampo(input)) {
            return false;
        }
        return true;
    }
}