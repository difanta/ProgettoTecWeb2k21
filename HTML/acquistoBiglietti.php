<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function printOrdine(&$htmlPage)
{
    $messaggi = "";
    $ordine = "";

    if (isset($_GET["id"])) {
        $id = $_GET["id"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            $results = $connection->getProiezioneRecap($id);
            $connection->closeConnection();

            $ordine = file_get_contents("template/templateProiezioneAcquisto.html");

            $ordine = str_replace("pNome", $results[0]["nome"], $ordine);
            $ordine = str_replace("pRegista", $results[0]["regista"], $ordine);
            $ordine = str_replace("pData", $results[0]["data"], $ordine);
            $ordine = str_replace("pOra", $results[0]["ora"], $ordine);
            
        } else {
            $messaggi .= "<li>problemi db</li>";
        }
    } else {
        $messaggi = "<p>proiezione non selezionata</p>";
    }

    $htmlPage = str_replace("<messaggi/>", $messaggi, $htmlPage);
    $htmlPage = str_replace("<ordine/>", $ordine, $htmlPage);
}

function insertOrdine(&$htmlPage)
{
    $messaggi = "";

    if (Login::is_logged()) {
        if ($_POST["method"] == "Acquista") {
            $proiezione = $_GET["id"];

            $connection = new DBAccess();
            $connectionOk = $connection->openDB();

            if ($connectionOk) {
                if ($connection->insertTicket($proiezione)) {
                    $messaggi = "<p>Biglietto acquistato con successo</p>";
                } else {
                    $messaggi = "<p>Errore nell aggiunta</p>";
                }
                $connection->closeConnection();
            } else {
                $messaggi = "<li>problemi db</li>";
            }
        }
    } else { // not logged
        $messaggi .= "<li>Utente non loggato</li>";
    }

    $htmlPage = str_replace("<messaggi/>", $messaggi, $htmlPage);
}

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();
    insertOrdine($htmlPage);

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header("HTTP/1.1 303 See Other");
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/acquistoBiglietti.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printOrdine($htmlPage);

    echo $htmlPage;
}
