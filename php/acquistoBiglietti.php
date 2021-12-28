<?php

session_start();

include "login.php";

use DB\DBAccess;

function printOrdine(&$htmlPage)
{
    $messaggi = "";
    $ordine = "";

    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        $nome = "";
        $regista = "";
        $data = "";
        $ora = "";

        // get data from db

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            $results = $connection->get("SELECT nome, regista, CAST(orario AS DATE) as data, TIME_FORMAT(CAST(orario AS TIME), '%H:%i') as ora FROM Proiezione join Film on film WHERE Proiezione.id='$id' and Proiezione.film = Film.id");
            $nome = $results[0]["nome"];
            $regista = $results[0]["regista"];
            $data = $results[0]["data"];
            $ora = $results[0]["ora"];

            $connection->closeConnection();
        } else {
            $messaggi .= "<li>problemi db<li/>";
        }

        $ordine = "<div id=\"ordine\">
        <h3 title=\"Titolo\">" . $nome . "</h3>
        <em title=\"Registi\">" . $regista . "</em>
        <p>
            <span class=\"data\">" . $data . "</span>
            <span class=\"ora\">" . $ora . "</span>
            <br>
            <span class=\"prezzo\">7.00€</span>
        </p>
        </div>";

    } else {
        $messaggi = "<p>proiezione non selezionata<p/>";
    }

    $htmlPage = str_replace("<messaggi/>", $messaggi, $htmlPage);
    $htmlPage = str_replace("<ordine/>", $ordine, $htmlPage);
}

function insertOrdine(&$htmlPage)
{
    $messaggi = "";

    if (Login::is_logged()) {
        if (isset($_POST["submit"])) {
            $proiezione = $_GET["id"];
            $utente = $_SESSION["login"];

            $connection = new DBAccess();
            $connectionOk = $connection->openDB();

            if ($connectionOk) {
                if ($connection->insert("insert into Biglietto(id, utente, proiezione) 
                                          values (DEFAULT,'$utente', '$proiezione');")) {
                    $messaggi = "<p>Biglietto acquistato con successo<p/>";
                } else {
                    $messaggi = "<p>Errore nell aggiunta<p/>";
                }
                $connection->closeConnection();
            } else {
                $messaggi = "<li>problemi db<li/>";
            }
        }
    } else { // not logged
        $messaggi .= "<li>Utente non loggato<li/>";
    }

    $htmlPage = str_replace("<messaggi/>", $messaggi, $htmlPage);
}

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header("HTTP/1.1 303 See Other");
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("../HTML/acquistoBiglietti.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    insertOrdine($htmlPage);
    printOrdine($htmlPage);


    echo $htmlPage;
}
