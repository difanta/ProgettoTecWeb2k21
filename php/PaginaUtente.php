<?php

session_start();

include "login.php";
use DB\DBAccess;

function printBiglietti(&$htmlPage)
{
    $listaBiglietti = "";

    if (Login::is_logged()) {

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            $biglietti = $connection->get("SELECT Film.nome, Biglietto.id, orario FROM Biglietto join Film join Proiezione join Utente on " . $_SESSION["login"]);
            $connection->closeConnection();
            if ($biglietti != null) {
                $listaBiglietti .= "<ul>";
                foreach ($biglietti as $biglietto) {
                    $listaBiglietti .=
                        "<li class=\"ticket\"><p><span class=\"ticket-up\">" . $biglietto["nome"] .
                        "</span><span class=\"ticket-up\">#" . $biglietto["id"] .
                        "</span></p><p><span class=\"ticket-low\">" . $biglietto["orario"] .
                        "</span></p></li>";
                }
                unset($biglietto);
                $listaBiglietti .= "</ul>";
            } else {
                $listaBiglietti .= "<p>Errore load biglietti</p>";
            }
        } else {
            $listaBiglietti .= "<p>Errore connessione db</p>";
        }
    } else { // not logged
        $listaBiglietti .= "<p>Utente non loggato</p>";
    }

    $htmlPage =  str_replace("<listaBiglietti/>", $listaBiglietti, $htmlPage);
}

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handle_login();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header("HTTP/1.1 303 See Other");
    header("Location: ./PaginaUtente.php");
} else /* GET */ {
    $htmlPage = file_get_contents("../HTML/paginaUtente.html");

    // show login/register/logout results
    Login::set_login_contents($htmlPage);
    printBiglietti($htmlPage);

    $htmlPage = str_replace("placeholder", "style", $htmlPage);

    echo $htmlPage;
}

