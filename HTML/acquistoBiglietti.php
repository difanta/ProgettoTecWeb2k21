<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

/**
 * Replaces <ordine/> with ticket's info
 * @request GET
 */
function printOrdine(&$htmlPage)
{
    $ordine = "";

    if (isset($_GET["id"])) {
        $id = $_GET["id"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            $results = $connection->getProiezioneRecap($id);
            $connection->closeConnection();

            $ordine = file_get_contents("template/templateProiezioneAcquisto.html");

            $ordine = str_replace("pNome", Sanitizer::forHtml($results[0]["nome"]), $ordine);
            $ordine = str_replace("pRegista", Sanitizer::forHtml($results[0]["regista"]), $ordine);
            $ordine = str_replace("pData", Sanitizer::forHtml($results[0]["data"]), $ordine);
            $ordine = str_replace("pOra", Sanitizer::forHtml($results[0]["ora"]), $ordine);

        } else {
            $ordine .= "<strong class='feedbackNegative'>Problemi di connessione al DB</strong>";
        }
    } else {
        $ordine = "<strong class='feedbackPositive'>proiezione non selezionata</strong>";
    }

    $htmlPage = str_replace("<ordine/>", $ordine, $htmlPage);
}

/**
 * Manage feedback on POST request, otherwise prints the buy button
 * @request GET
 */
function printAcquisto(&$htmlPage)
{
    print_r($_SESSION);
    $acquisto = "";
    if (isset($_SESSION["method"])
        && isset($_SESSION["success"])) {
        if ($_SESSION["method"] == "Acquista") {

            if ($_SESSION["success"]) {
                $acquisto = file_get_contents("template/templateAcquistoSuccesso.html");
            } else {
                $feedback = isset($_SESSION["feedback"]) ? Sanitizer::forHtml($_SESSION["feedback"]) : "";
                $acquisto = "<strong class='feedbackNegative'>" . $feedback . "</strong>";
            }
            unset($_SESSION["method"]);
            unset($_SESSION["feedback"]);
            unset($_SESSION["success"]);
        }
    } else { // first print
        $acquisto = file_get_contents("template/templateAcquisto.html");
    }

    $htmlPage = str_replace("<acquisto/>", $acquisto, $htmlPage);
}

/**
 * Insert ticket into db
 * @request POST
 */
function insertOrdine(&$htmlPage)
{
    if (!Login::is_logged()) return;

    $_SESSION["method"] = "Acquista";
    $proiezione = $_GET["id"];

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if ($connectionOk) {
        if ($connection->insertTicket($proiezione)) {
            $_SESSION["success"] = true;
        } else {
            $_SESSION["success"] = false;
            $feedback = "Errore nell'operazione";
        }
        $connection->closeConnection();
    } else {
        $_SESSION["success"] = false;
        $feedback = "problemi db";
    }

    $htmlPage = str_replace("<feedback/>", $feedback, $htmlPage);
}

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    if ($_POST["method"] == "Acquista") {
        insertOrdine($htmlPage);
    }

    http_response_code(303);

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header("Location: " . $_SERVER["REQUEST_URI"]);

} else /* GET */ {
    $htmlPage = file_get_contents("template/acquistoBiglietti.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);

    printOrdine($htmlPage);
    printAcquisto($htmlPage);

    echo $htmlPage;
}
