
/* Header -------------------------------------------- */



/* Lista Film ---------------------------------------- */

function listaFilmLike(checked, id) {
    elem = document.getElementById('label-'+id);
    if(checked) {
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
    if(cdc_st) { if(!lcdc.classList.contains('selected')) lcdc.classList.add('selected'); }
    else lcdc.classList.remove('selected');

    sastispay_st = document.getElementById('satispay').checked;
    lsatispay = document.getElementById('label-satispay');
    if(sastispay_st) { if(!lsatispay.classList.contains('selected')) lsatispay.classList.add('selected'); }
    else lsatispay.classList.remove('selected');
    
    paypal_st = document.getElementById('paypal').checked;
    lpaypal = document.getElementById('label-paypal');
    if(paypal_st) { if(!lpaypal.classList.contains('selected')) lpaypal.classList.add('selected'); }
    else lpaypal.classList.remove('selected'); 
}