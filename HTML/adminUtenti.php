<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function printUtentiAndFilms(&$htmlPage) {
    $p_utenti = "<utenti/>";
    $p_nomifilm = "<nomifilm/>";

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $template = "<option value=\"utente\">utente</option>";
        $stringa = "";
        $utenti = $connection->getEmailUtenti();
        foreach($utenti as $utente) {
            $stringa .= str_replace("utente", Sanitizer::forHtml($utente["email"]), $template);
        }
        $htmlPage = str_replace($p_utenti, $stringa, $htmlPage);

        $template = "<option value=\"nomefilm\">nomefilm</option>";
        $stringa = "";
        $films = $connection->getNomiFilmApprovati();
        foreach($films as $film) {
            $stringa .= str_replace("nomefilm", Sanitizer::forHtml($film["nome"]), $template);
        }
        $htmlPage = str_replace($p_nomifilm, $stringa, $htmlPage);
    } else {
        $htmlPage = str_replace($p_utenti, "", $htmlPage);
        $htmlPage = str_replace($p_nomifilm, "", $htmlPage);
    }
}

function printBiglietti() {
    if(!Login::is_logged_admin()) { return; }

    $p_biglietto = "<biglietto/>";

    /*$connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $template = file_get_contents("./template/templateAdminUtentiBiglietto.html");
        $stringa = "";
        $utenti = $connection->();
        foreach($utenti as $utente) {
            $stringa .= str_replace("utente", $utente["email"], $template);
        }
        $htmlPage = str_replace($p_biglietto, $stringa, $htmlPage);
    } else {
        $htmlPage = str_replace($p_biglietto, "", $htmlPage);
    }*/
}

function printAggiungiBiglietto(&$htmlPage) {
    $htmlPage = str_replace("agg_nomefilmselezionato", "", $htmlPage);
    $htmlPage = str_replace("agg_idproiezioneselezionata", "", $htmlPage);
}

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/adminUtenti.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printUtentiAndFilms($htmlPage);
    printBiglietti();
    printAggiungiBiglietto($htmlPage);

    echo $htmlPage;
}