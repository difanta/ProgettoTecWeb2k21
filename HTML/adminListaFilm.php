<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function returnFilm() {

}

function aggiungiFilm() {

}

function modificaFilm() {

}

function printFilms(&$htmlPage) {
    $p_nomifilm = "<nomifilm/>";

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $template = "<option value=\"nomefilm\">nomefilm</option>";
        $stringa = "";
        $films = $connection->getNomiFilm();
        foreach($films as $film) {
            $stringa .= str_replace("nomefilm", $film["nome"], $template);
        }
        $htmlPage = str_replace($p_nomifilm, $stringa, $htmlPage);
    } else {
        $htmlPage = str_replace($p_nomifilm, "", $htmlPage);
    }
}

function printAggiungiFilm(&$htmlPage) {

}

function printModificaFilm(&$htmlPage) {

}

if(!isset($_POST["method"]) && isset($_POST["mod_nomeFilm"])) {
    echo returnFilm();
} else {
    if(isset($_POST["method"])) {
        // handle login/register/logout POST request
        Login::handleLogin();
        aggiungiFilm();
        modificaFilm();

        // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
        http_response_code(303);
        header("Location: " . $_SERVER["REQUEST_URI"]);
    } else /* GET */ {
        $htmlPage = file_get_contents("template/adminListaFilm.html");

        // show login/register/logout results
        Login::printLogin($htmlPage);
        printFilms($htmlPage);
        printAggiungiFilm($htmlPage);
        printModificaFilm($htmlPage);

        echo $htmlPage;
    }
}

?>