window.addEventListener("load", caricaPagina, true);
function caricaPagina(){ caricamento();};

/* Feature Detection --------------------------------- */

let passiveSupported = false;

try {
    const options = {
        get passive() { // This function will be called when the browser
            //   attempts to access the passive property.
            passiveSupported = true;
            return false;
        }
    };

    window.addEventListener("test", null, options);
    window.removeEventListener("test", null, options);
} catch (err) {
    passiveSupported = false;
}

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

// hide account dropdown on click outside it and outside account button
document.addEventListener('click', function (e) {
    let accDropdown = document.getElementById("accountDropdown");
    let accBtn = document.getElementById("accountButton");
    let target = e.target;
    while (target && target != accDropdown && target != accBtn) {
        target = target.parentNode;
    }

    if (target == null && accDropdown.classList.contains("dropdown")) {
        toggleAccountDropdown();
    }
}, passiveSupported ? {passive: true} : false);


//hide account dropdown on ESC keydown
document.addEventListener("keydown", function (e) {
    if (e.code === "Escape" && document.getElementById("accountDropdown").classList.contains("dropdown")) {
        toggleAccountDropdown();
    }
}, passiveSupported ? {passive: true} : false);


// display and hide tornaSu based on scoll distance from top
window.addEventListener("scroll", function () {
    elem = document.getElementById("tornaSu");
    if (document.body.scrollTop > 250 || document.documentElement.scrollTop > 250) {
        elem.style.display = "block";
    } else {
        elem.style.display = "none";
    }
}, passiveSupported ? {passive: true} : false);

/* Lista Film ---------------------------------------- */

function listaFilmLike(checked, nomeFilm) {
    elem = document.getElementById('label-like-' + nomeFilm);
    if (checked == true) {
        elem.innerHTML = elem.innerHTML.replace('favorite_border', 'favorite');
    } else {
        elem.innerHTML = elem.innerHTML.replace('favorite', 'favorite_border');
    }

    let request = new XMLHttpRequest();
    request.onload = (e) => {
        if (request.readyState === request.DONE) {
            let select = document.getElementById("apSelectP");
            if (request.status === 200) {
            } else {
            }
        }
    };
    request.onerror = (e) => {
    }
    request.open("POST", "like.php");
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(encodeURIComponent("nomeFilm") + "=" + encodeURIComponent(nomeFilm)
        + "&" +
        encodeURIComponent("like") + "=" + encodeURIComponent(checked));
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

function showHideButton(id) {
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
    document.getElementById('userInfoNome').removeAttribute('disabled');
    document.getElementById('userInfoCognome').removeAttribute('disabled');
    document.getElementById('userInfoDataNascita').removeAttribute('disabled');
    document.getElementById('userInfoEmail').removeAttribute('disabled');
    document.getElementById('userInfoPassword').removeAttribute('disabled');
}

function setEditOff() {
    showHideButton('buttonInvia');
    showHideButton('buttonReset');
    showHideButton('buttonAnnulla');
    showHideButton('buttonModifica');
    document.getElementById('userInfoForm').reset();
    document.getElementById('userInfoNome').setAttribute('disabled', '');
    document.getElementById('userInfoCognome').setAttribute('disabled', '');
    document.getElementById('userInfoDataNascita').setAttribute('disabled', '');
    document.getElementById('userInfoEmail').setAttribute('disabled', '');
    document.getElementById('userInfoPassword').setAttribute('disabled', '');
}

/* Admin */

function injectProiezioni(elem, target) {
    if(!elem || !target) return;
    
    if(elem.getAttribute("selection") && elem.getAttribute("selection") != "") {
        elem.options[elem.selectedIndex].selected = false;
        const option = [].filter.call(elem.options, option => (option.value == elem.getAttribute("selection")))[0].selected = true;
        elem.setAttribute("selection", "");
    }

    if(!elem.value) return;

    target.innerHTML = "";

    let request = new XMLHttpRequest();
    request.onload = (e) => {
        if (request.readyState === request.DONE) {
            if (request.status === 200) {
                target.innerHTML = request.responseText;

                // no proiezioni available -> cancel proiezione selection
                if(!target.options[target.selectedIndex]) {
                    target.setAttribute("selection", "");
                }

                // load default proiezione
                if(target.getAttribute("selection") && target.getAttribute("selection") != "") {
                    target.options[target.selectedIndex].selected = false;
                    [].filter.call(target.options, option => (option.value == target.getAttribute("selection")))[0].selected = true;
                    target.setAttribute("selection", "");
                }

                target.dispatchEvent(new Event("change"));
            } else {
            }
        }
    };
    request.onerror = (e) => {
    }
    request.open("POST", "getproiezioni.php");
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(encodeURIComponent(elem.getAttribute("name")) + "=" + encodeURIComponent(elem.value));

}

/* Admin Utenti */

function initAdminUtenti() {
    let elements = document.getElementsByClassName("js_toBeInit");
    
    for (var i=0; i < elements.length; i++) {
        elements[i].dispatchEvent(new Event("change"));
    }
}

function onUtenteSelected(elem) {
    if(!elem) return;

    if (elem.getAttribute("selection") && elem.getAttribute("selection") != "") {
        elem.options[elem.selectedIndex].selected = false;
        [].filter.call(elem.options, option => (option.value == elem.getAttribute("selection")))[0].selected = true;
        elem.setAttribute("selection", "");
    }

    let url = new URL(window.location);

    if(!elem.value || elem.value == "") {
        if(!url.searchParams.has("username")) return;
        url.searchParams.delete("username");
    } else {
        if(url.searchParams.has("username") && url.searchParams.get("username") == elem.value) return;
        url.searchParams.set("username", elem.value);
    }
    window.location = url;
}

/* Admin Lista Film */

function initAdminListaFilm() {
    elem = document.getElementById("alfSelect");

    if (!elem.options[elem.selectedIndex]) {
        elem.setAttribute("selection", "");
        return;
    }

    if (elem.getAttribute("selection") && elem.getAttribute("selection") != "") {
        elem.options[elem.selectedIndex].selected = false;
        [].filter.call(elem.options, option => (option.value == elem.getAttribute("selection")))[0].selected = true;
        elem.setAttribute("selection", "");
    } else {
        elem.dispatchEvent(new Event("change"));
    }
}

function resetAlfm() {
    document.getElementById("alfmTitolo").setAttribute("value", "");
    document.getElementById("alfmProduttore").setAttribute("value", "");
    document.getElementById("alfmRegisti").setAttribute("value", "");
    document.getElementById("alfmAnno").setAttribute("value", "");
    document.getElementById("alfmDurata").setAttribute("value", "");
    document.getElementById("alfmAlt").innerHTML = "";
    document.getElementById("alfmDescrizione").innerHTML = "";
    document.getElementById("alfmCast").innerHTML = "";
    document.getElementById("alfmGara").checked = false;
    document.getElementById("alfmApprovato").checked = false;
}

function mod_onFilmChanged() {
    elem = document.getElementById("alfSelect");

    if (!elem.options[elem.selectedIndex]) {
        return;
    }

    resetAlfm();

    let request = new XMLHttpRequest();
    request.onload = (e) => {
        if (request.readyState === request.DONE) {
            if (request.status === 200) {
                let obj = JSON.parse(request.response);

                let titolo = document.getElementById("alfmTitolo");
                titolo.setAttribute("value", obj["nome"]);
                titolo.value = titolo.getAttribute("value");
                titolo.className = "normalInput";

                let produttore = document.getElementById("alfmProduttore");
                produttore.setAttribute("value", obj["produttore"]);
                produttore.value = produttore.getAttribute("value");
                produttore.className = "normalInput";

                let registi = document.getElementById("alfmRegisti");
                registi.setAttribute("value", obj["regista"]);
                registi.value = registi.getAttribute("value");
                registi.className = "normalInput";

                let anno = document.getElementById("alfmAnno");
                anno.setAttribute("value", obj["anno"]);
                anno.value = anno.getAttribute("value");
                anno.className = "normalInput";

                let durata = document.getElementById("alfmDurata");
                durata.setAttribute("value", obj["durata"]);
                durata.value = durata.getAttribute("value");
                durata.className = "normalInput";

                let alt = document.getElementById("alfmAlt");
                alt.innerHTML = obj["alt"];
                alt.value = alt.innerHTML;
                alt.className = "normalInput";

                let descrizione = document.getElementById("alfmDescrizione");
                descrizione.innerHTML = obj["descrizione"];
                descrizione.value = descrizione.innerHTML;
                descrizione.className = "normalInput";

                let cast = document.getElementById("alfmCast");
                cast.innerHTML = obj["cast"];
                cast.value = cast.innerHTML;
                cast.className = "normalInput";

                document.getElementById("alfmGara").checked = (obj["in_gara"] == 1);
                document.getElementById("alfmApprovato").checked = (obj["approvato"] == 1);
            }
        }
    };
    request.onerror = (e) => {
    }
    request.open("POST", window.location.href);
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(encodeURIComponent(elem.getAttribute("name")) + "=" + encodeURIComponent(elem.value));
}

/* Admin Proiezioni */

function initAdminProiezioni() {
    elem = document.getElementById("apraSelect");
    if(!elem || !elem.options[elem.selectedIndex]) { return; }

    if (elem.getAttribute("selection") && elem.getAttribute("selection") != "") {
        elem.options[elem.selectedIndex].selected = false;
        [].filter.call(elem.options, option => (option.value == elem.getAttribute("selection")))[0].selected = true;
        elem.setAttribute("selection", "");
    }

    elem = document.getElementById("apSelect");
    elem.dispatchEvent(new Event("change"));
}

function mod_ProiezioneSelected() {
    elem = document.getElementById("apSelectP");

    let selectedFilm = document.getElementById("apSelect").value;

    // load default film in the form select
    if(selectedFilm && selectedFilm != "") { 
        const selectFilm = document.getElementById("aprmSelect");
        selectFilm.options[selectFilm.selectedIndex].selected = false;
        [].filter.call(selectFilm.options, option => (option.value == selectedFilm))[0].selected = true;
    }

    // load default date in the form if at least one proiezione is available and if attribute orario is defined
    if(elem.options[elem.selectedIndex] && elem.options[elem.selectedIndex].getAttribute("orario") && elem.options[elem.selectedIndex].getAttribute("orario") != "") {
        document.getElementById("aprmData").setAttribute("value", elem.options[elem.selectedIndex].getAttribute("orario").replace(/\s/g, 'T'));
    } else {
        document.getElementById("aprmData").setAttribute("value", "");
    }

}


/*---  validazione form lato client --------------------------- */

/*
chiave:campo input di cui inserisco informazioni
[0] : place holder
[1] : espressione regolare
[2] : messaggio di errore
*/
var emailregex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
var emailregexlogin = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))|admin|user$/;
var namesRegex = /^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+$/;
var passwordregex = /^(?=.*\d)(?=.*[a-zA-Z])[0-9a-zA-Z]{8,}$/;
var passwordregexlogin = /^(?=.*\d)(?=.*[a-zA-Z])[0-9a-zA-Z]{8,}|admin|user$/;
var dettagli_form = {
    // contest
    "titolo": ["inserisci il titolo del film", /[\wàèéìòù]{1,}/, "il titolo deve contenere almeno un carattere alfanumerico"],
    "descrizione": ["inserisci una descrizione del film", /^.{10,}$/, "la descrizione deve contenere almeno dieci caratteri"],
    "durata": ["", /^[6-9][0-9]|1[0-7][0-9]|180$/, "la durata deve essere compresa tra i 60 ed i 180 minuti"],
    "anno": ["", /^19[0-9][0-9]|20[0-1][0-9]|202[0-2]$/, "l'anno deve essere compreso tra il 1900 ed il 2022"],
    "regista": ["inserisci il regista del film", /[a-zA-Zàèéìòù]{1,}/, "il regista deve contenere almeno un carattere alfabetico"],
    "produttore": ["inserisci il produttore del film", /[a-zA-Zàèéìòù]{1,}/, "il produttore deve contenere almeno un carattere alfabetico"],
    "cast": ["inserisci il cast del film separato da virgole", /^.{5,}$/, "il cast deve contenere almeno cinque caratteri"],
    //listaFilm
    "alfaTitolo": ["inserisci il titolo del film", /[\wàèéìòù]{1,}/, "il titolo deve contenere almeno un carattere alfanumerico"],
    "alfaDescrizione": ["inserisci una descrizione del film", /^.{10,}$/, "la descrizione deve contenere almeno dieci caratteri"],
    "alfaDurata": ["", /^[6-9][0-9]|1[0-7][0-9]|180$/, "la durata deve essere compresa tra i 60 ed i 180 minuti"],
    "alfaAnno": ["", /^19[0-9][0-9]|20[0-1][0-9]|202[0-2]$/, "l'anno deve essere compreso tra il 1900 ed il 2022"],
    "alfaRegisti": ["inserisci il regista del film", /[a-zA-Zàèéìòù]{1,}/, "il regista deve contenere almeno un carattere alfabetico"],
    "alfaProduttore": ["inserisci il produttore del film", /[a-zA-Zàèéìòù]{1,}/, "il produttore deve contenere almeno un carattere alfabetico"],
    "alfaCast": ["inserisci il cast del film separato da virgole", /^.{5,}$/, "il cast deve contenere almeno cinque caratteri"],
    "alfmTitolo": ["inserisci il titolo del film", /[\wàèéìòù]{1,}/, "il titolo deve contenere almeno un carattere alfanumerico"],
    "alfmDescrizione": ["inserisci una descrizione del film", /^.{10,}$/, "la descrizione deve contenere almeno dieci caratteri"],
    "alfmDurata": ["", /^[6-9][0-9]|1[0-7][0-9]|180$/, "la durata deve essere compresa tra i 60 ed i 180 minuti"],
    "alfmAnno": ["", /^19[0-9][0-9]|20[0-1][0-9]|202[0-2]$/, "l'anno deve essere compreso tra il 1900 ed il 2022"],
    "alfmRegisti": ["inserisci il regista del film", /[a-zA-Zàèéìòù]{1,}/, "il regista deve contenere almeno un carattere alfabetico"],
    "alfmProduttore": ["inserisci il produttore del film", /[a-zA-Zàèéìòù]{1,}/, "il produttore deve contenere almeno un carattere alfabetico"],
    "alfmCast": ["inserisci il cast del film separato da virgole", /^.{5,}$/, "il cast deve contenere almeno cinque caratteri"],
    // pagina utente
    "userInfoNome": ["inserisci il tuo nome", namesRegex, "nome non valido"],
    "userInfoCognome": ["inserisci il tuo cognome", namesRegex, "cognome non valido"],
    "userInfoEmail": ["inserisci la tua mail", emailregexlogin, "mail non valida"],
    "userInfoPassword": ["", passwordregexlogin, "la password deve contenere almeno una lettera ed un numero"],
    // content login
    "contentLoginEmail": ["inserisci la tua mail", emailregexlogin, "mail non valida"],
    "contentLoginPassword": ["", passwordregexlogin, "la password deve contenere almeno una lettera ed un numero ed essere lunga almeno otto caratteri"],
    // content signup
    "contentSingupNome": ["inserisci il tuo nome", namesRegex, "nome non valido"],
    "contentSingupCognome": ["inserisci il tuo cognome", namesRegex, "cognome non valido"],
    "contentSingupEmail": ["inserisci la tua mail", emailregexlogin, "mail non valida"],
    "contentSingupPassword": ["", passwordregexlogin, "la password deve contenere almeno una lettera ed un numero ed essere lunga almeno otto caratteri"],
    "contentSingupPassword2": ["", passwordregexlogin, "la password non coincide con la conferma password"],
    //login
    "loginEmail": ["inserisci la tua mail", emailregexlogin, "mail non valida"],
    "loginPassword": ["", passwordregexlogin, "almeno 8 caratteri di cui un numero e una lettera"],
    //signup
    "signupNome": ["inserisci il tuo nome", namesRegex, "nome non valido"],
    "signupCognome": ["inserisci il tuo cognome", namesRegex, "cognome non valido"],
    "signupEmail": ["inserisci la tua mail", emailregexlogin, "mail non valida"],
    "signupPassword": ["", passwordregexlogin, "la password deve contenere almeno una lettera ed un numero ed essere lunga almeno otto caratteri"],
    "signupPassword2": ["", passwordregexlogin, "la password non coincide con la conferma password"],
    // admin candidature
    "candidaturaAlt": ["inserisci la descrizione alternativa dell'immagine", namesRegex, "descrizione non valida"],
}

function caricamento() {
    for (var key in dettagli_form) {
        var input = document.getElementById(key);
        if (input) { // if user is not logged and forms are not displayed this value will be NULL
            campoDefault(input);
            input.onfocus = function () {
                campoPerInput(this);
            };
            input.onblur = function () {
                validazioneCampo(this);
            };
        }
    }
}

function campoDefault(input) {
    if (input.value == "" && (!input.getAttribute("value") || input.getAttribute("value") == "")) {
        input.className = "placeholderText";
        input.value = dettagli_form[input.id][0];
    }
}

function campoPerInput(input) {
    if (input.value == dettagli_form[input.id][0]) {
        input.className = "normalInput";
        input.value = "";
    }
}

function validazioneCampo(input) {
    if(input.id=="signupPassword2"){
        return confermaPassword(input);
    }
    var padre = input.parentNode;
    if (padre.children.length == 2) {
        padre.removeChild(padre.children[1]);
    }

    var regex = dettagli_form[input.id][1];
    var text = input.value;
    input.value = text;
    if ((text.search(regex) == -1) || (text == dettagli_form[input.id][0])) {
        mostraErrore(input);
        return false;
    }
    return true;
}
function confermaPassword(input){
    var padre = input.parentNode;
    if (padre.children.length == 2) {
        padre.removeChild(padre.children[1]);
    }
    var pw = document.getElementById("signupPassword").value;
    var text = input.value;
    input.value = text;
    if (text!=pw) {
        mostraErrore(input);
        return false;
    }
    return true;
}

function mostraErrore(input) {
    var padre = input.parentNode;
    var errore = document.createElement("strong");
    errore.className = "errorSuggestion";
    errore.appendChild(document.createTextNode(dettagli_form[input.id][2]));
    padre.appendChild(errore);
}

function isAncestorOf(elem, ancestor) {
    while (elem && elem != ancestor) {
        elem = elem.parentNode;
    }
    if(elem) return true;
    else return false;
}

function validazioneForm(form) {
    let truth = true;
    for (var key in dettagli_form) {
        var input = document.getElementById(key);
        if (input && isAncestorOf(input, form) && !validazioneCampo(input)) {
            truth = false;
            console.log(input);
        }
    }
    return truth;
}