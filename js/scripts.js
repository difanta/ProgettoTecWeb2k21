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

function expandMenu(){
    if(document.getElementsByClassName('secondaryMenuItem')[0].style.display == "none"){
        document.getElementsByClassName('secondaryMenuItem')[0].style.display = "block";
        document.getElementsByClassName('secondaryMenuItem')[1].style.display = "block";
        document.getElementsByClassName('secondaryMenuItem')[2].style.display = "block";
        document.getElementById("expandMenuArrow").textContent = "expand_less";
    }
    else{
        document.getElementsByClassName('secondaryMenuItem')[0].style.display = "none";
        document.getElementsByClassName('secondaryMenuItem')[1].style.display = "none";
        document.getElementsByClassName('secondaryMenuItem')[2].style.display = "none";
        document.getElementById("expandMenuArrow").textContent = "expand_more";
    }
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
    }
    return true;
}

/* Admin Lista Film */

function mod_initFilm() {
    elem = document.getElementById("alfSelect");

    if(!elem.options[elem.selectedIndex]) {
        elem.setAttribute("selection", "");
        return;
    }

    if(elem.getAttribute("selection") && elem.getAttribute("selection") != "") {
        elem.options[elem.selectedIndex].selected = false;
        [].filter.call(elem.options, option => (option.value == elem.getAttribute("selection")))[0].selected = true;
        elem.setAttribute("selection", "");
    }
    mod_onFilmChanged();
}

function resetAlfm() {
    document.getElementById("alfmTitolo").setAttribute("value", "");
    document.getElementById("alfmProduttore").setAttribute("value", "");
    document.getElementById("alfmRegisti").setAttribute("value", "");
    document.getElementById("alfmAnno").setAttribute("value", "");
    document.getElementById("alfmDurata").setAttribute("value", "");
    document.getElementById("alfmDescrizione").innerHTML = "";
    document.getElementById("alfmCast").innerHTML = "";
    document.getElementById("alfmGara").checked = false;
    document.getElementById("alfmApprovato").checked = false;
}

function mod_onFilmChanged() {
    elem = document.getElementById("alfSelect");

    if(!elem.options[elem.selectedIndex]) { return; }

    let request = new XMLHttpRequest();
    request.onload = (e) => {
        if (request.readyState === request.DONE) {
            if (request.status === 200) {
                let obj = JSON.parse(request.response);
                document.getElementById("alfmTitolo").setAttribute("value", obj["nome"]);
                document.getElementById("alfmProduttore").setAttribute("value", obj["produttore"]);
                document.getElementById("alfmRegisti").setAttribute("value", obj["regista"]);
                document.getElementById("alfmAnno").setAttribute("value", obj["anno"]);
                document.getElementById("alfmDurata").setAttribute("value", obj["durata"]);
                document.getElementById("alfmDescrizione").innerHTML = obj["descrizione"];
                document.getElementById("alfmCast").innerHTML = obj["cast"];
                document.getElementById("alfmGara").checked = (obj["in_gara"] == 1);
                document.getElementById("alfmApprovato").checked = (obj["approvato"] == 1);
            } else {
                resetAlfm();
            }
        }
    };
    request.onerror = (e) => {
        resetAlfm();
    }
    request.open("POST", window.location.href);
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(encodeURIComponent(elem.getAttribute("name")) + "=" + encodeURIComponent(elem.value));

    resetAlfm();
}
 
/* Admin Proiezioni */

function agg_initFilm() {
    elem = document.getElementById("apraSelect");

    if(!elem.options[elem.selectedIndex]) { return; }

    if(elem.getAttribute("selection") && elem.getAttribute("selection") != "") {
        elem.options[elem.selectedIndex].selected = false;
        [].filter.call(elem.options, option => (option.value == elem.getAttribute("selection")))[0].selected = true;
        elem.setAttribute("selection", "");
    }
}

function mod_FilmSelected() {
    elem = document.getElementById("apSelect");

    if(!elem.options[elem.selectedIndex]) { return; }

    if(elem.getAttribute("selection") && elem.getAttribute("selection") != "") {
        elem.options[elem.selectedIndex].selected = false;
        const option = [].filter.call(elem.options, option => (option.value == elem.getAttribute("selection")))[0].selected = true;
        elem.setAttribute("selection", "");
    }

    let request = new XMLHttpRequest();
    request.onload = (e) => {
        if (request.readyState === request.DONE) {
            let select = document.getElementById("apSelectP");
            if (request.status === 200) {
                select.innerHTML = request.responseText;
            } else {
                select.innerHTML = "";
            }
            mod_ProiezioneSelected(select);
        }
    };
    request.onerror = (e) => {
        document.getElementById("apSelectP").innerHTML = "";
    }
    request.open("POST", window.location.href);
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(encodeURIComponent(elem.getAttribute("name")) + "=" + encodeURIComponent(elem.value));

    document.getElementById("apSelectP").innerHTML = "";
}

function mod_ProiezioneSelected() {
    elem = document.getElementById("apSelectP");

    // no proiezioni available -> cancel proiezione selection
    if(!elem.options[elem.selectedIndex]) {
        elem.setAttribute("selection", "");
    }

    // load default proiezione
    if(elem.getAttribute("selection") && elem.getAttribute("selection") != "") {
        elem.options[elem.selectedIndex].selected = false;
        [].filter.call(elem.options, option => (option.value == elem.getAttribute("selection")))[0].selected = true;
        elem.setAttribute("selection", "");
    }

    let selectedFilm = document.getElementById("apSelect").value;

    // load default film
    if(selectedFilm && selectedFilm != "") { 
        const selectFilm = document.getElementById("aprmSelect");
        selectFilm.options[selectFilm.selectedIndex].selected = false;
        [].filter.call(selectFilm.options, option => (option.value == selectedFilm))[0].selected = true;
    }

    // load default date if at least one proiezione is available and if attribute orario is defined
    if(elem.options[elem.selectedIndex] && elem.options[elem.selectedIndex].getAttribute("orario") && elem.options[elem.selectedIndex].getAttribute("orario") != "") {
        document.getElementById("aprmData").setAttribute("value", elem.options[elem.selectedIndex].getAttribute("orario").replace(/\s/g, 'T'));
    } else {
        document.getElementById("aprmData").setAttribute("value", "");
    }

}