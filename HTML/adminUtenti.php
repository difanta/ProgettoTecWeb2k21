<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function printUtenti(&$htmlPage) {
    $p_utenti = "<utenti/>";

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $template = "<option value=\"utente\">utente</option>";
        $stringa = "";
        $utenti = $connection->getEmailUtenti();
        foreach($utenti as $utente) {
            $stringa .= str_replace("utente", $utente["email"], $template);
        }
        $htmlPage = str_replace($p_utenti, $stringa, $htmlPage);
    } else {
        $htmlPage = str_replace($p_utenti, "", $htmlPage);
    }
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
    printUtenti($htmlPage);

    echo $htmlPage;
}