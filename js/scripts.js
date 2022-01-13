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
} catch(err) {
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

// hide account dropdown on click outside it and outside account button
document.addEventListener('click', function(e) {
    let accDropdown = document.getElementById("accountDropdown");
    let accBtn = document.getElementById("accountButton");
    let target = e.target;
    while(target && target != accDropdown && target != accBtn) {
        target = target.parentNode;
    }

    if(target == null && accDropdown.classList.contains("dropdown")) {
        toggleAccountDropdown();
    }
}, passiveSupported ? { passive: true } : false);


//hide account dropdown on ESC keydown
document.addEventListener("keydown", function(e) {
    if(e.code === "Escape" && document.getElementById("accountDropdown").classList.contains("dropdown")) {
        toggleAccountDropdown();
    }
}, passiveSupported ? { passive: true } : false);


// display and hide tornaSu based on scoll distance from top
window.addEventListener("scroll", function() {
    elem = document.getElementById("tornaSu");
    if (document.body.scrollTop > 250 || document.documentElement.scrollTop > 250) {
        elem.style.display = "block";
    } else {
        elem.style.display = "none";
    }
}, passiveSupported ? { passive: true } : false);

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

/* Admin Utenti */

function onUtenteSelected() {
    var elem = document.getElementById('utentiSelect');

    if (elem.value !== "") {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var results = JSON.parse(xmlhttp.responseText);
                console.log(results);
                // utente
                document.getElementById("id").innerHTML = "id: " + results[0][0].id;
                document.getElementById("email").innerHTML = "email: " + results[0][0].email;
                document.getElementById("nome").innerHTML = "nome: " + results[0][0].nome;
                document.getElementById("cognome").innerHTML = "cognome: " + results[0][0].cognome;

                // tickets
                //document.getElementById("strumentoProva").value = results[1][0].nome;

                var list = document.getElementById("ticketList");
                var re = results[1];
                for(let i=0; i<results[1].length; i++){
                    let li = document.createElement("li");
                }

            }
        };
        xmlhttp.open("GET", "../php/onUtenteSelected.php?email=" + elem.value, true);
        xmlhttp.send();
    }
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
    } else {
        mod_onFilmChanged();
    }
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

    resetAlfm();

    let request = new XMLHttpRequest();
    request.onload = (e) => {
        if (request.readyState === request.DONE) {
            if (request.status === 200) {
                console.log(request.response);
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

    document.getElementById("apSelectP").innerHTML = "";

    let request = new XMLHttpRequest();
    request.onload = (e) => {
        if (request.readyState === request.DONE) {
            let select = document.getElementById("apSelectP");
            if (request.status === 200) {
                select.innerHTML = request.responseText;
            } else {
            }
            mod_ProiezioneSelected(select);
        }
    };
    request.onerror = (e) => {
    }
    request.open("POST", window.location.href);
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(encodeURIComponent(elem.getAttribute("name")) + "=" + encodeURIComponent(elem.value));
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


/*---  validazione form lato client --------------------------- */

/*
chiave:campo input di cui inserisco informazioni
[0] : place holder
[1] : espressione regolare
[2] : messaggio di errore
*/
var emailregex=/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
var dettagli_form = {
	"titolo" : ["inserisci il titolo del film",/[\wàèéìòù]{1,}/,"il titolo deve contenere almeno un carattere alfanumerico"], 
    "descrizione" : ["inserisci una descrizione del film",/^.{10,}$/,"la descrizione deve contenere almeno dieci caratteri"], 
    "durata" : ["",/^[6-9][0-9]|1[0-7][0-9]|180$/,"la durata deve essere compresa tra i 60 ed i 180 minuti"],
    "anno" : ["",/^19[0-9][0-9]|20[0-1][0-9]|202[0-2]$/,"l'anno deve essere compreso tra il 1900 ed il 2022"],
    "regista" : ["inserisci il regista del film",/[a-zA-Zàèéìòù]{1,}/,"il regista deve contenere almeno un carattere alfabetico"],
    "produttore" : ["inserisci il produttore del film",/[a-zA-Zàèéìòù]{1,}/,"il produttore deve contenere almeno un carattere alfabetico"],
    "cast" : ["inserisci il cast del film separato da virgole",/^.{5,}$/,"il cast deve contenere almeno cinque caratteri"],
    "email" : ["inserisci la tua mail",emailregex,"mail non valida"]
};

function caricamento(){
	for(var key in dettagli_form){
		var input = document.getElementById(key);
		campoDefault(input);
		input.onfocus=function(){campoPerInput(this);};
		input.onblur = function(){validazioneCampo(this);};
	}
}

function campoDefault(input){
	if (input.value == ""){
		input.className="placeholderText";
		input.value = dettagli_form[input.id][0];
	}
}

function campoPerInput(input){
	if (input.value == dettagli_form[input.id][0]){
		input.className="normalInput";
		input.value = "";
	}
}

function validazioneCampo(input){
	var padre=input.parentNode;	
	if(padre.children.length == 2){
		padre.removeChild(padre.children[1]);
	}
	
	var regex = dettagli_form[input.id][1];
	var text = input.value;
    input.value=text;
	if((text.search(regex) == -1) || (text==dettagli_form[input.id][0])) {
		mostraErrore(input);
		input.focus();
		return false;
	}
	return true;
}

function mostraErrore(input){
	var padre = input.parentNode;
	var errore = document.createElement("strong");
	errore.className="errorSuggestion";
	errore.appendChild(document.createTextNode(dettagli_form[input.id][2]));
	padre.appendChild(errore);
}

function validazioneForm(){
	for (var key in dettagli_form){
        var input=document.getElementById(key);
        if(!validazioneCampo(input)){
            return false;
        }
    }
    return true;
}