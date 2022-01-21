<?php

session_start();

include "../php/login.php";
include "../php/fs.php";

use DB\DBAccess;

/**
 * Replaces <listaCandidature/> with candidature's list
 * @request GET
 */
function printCandidature(&$htmlPage)
{
    $list = "";

    $c_sospesa_sel = "sospesaSel";
    $c_approvata_sel = "approvataSel";

    $filter_candidatura = "Sospesa";

    if (isset($_GET["filterCandidatura"])) {
        $filter_candidatura = $_GET["filterCandidatura"];
    }
    switch ($filter_candidatura) {
        case 'Sospesa':
            $htmlPage = str_replace($c_sospesa_sel, "selected", $htmlPage);
            $htmlPage = str_replace($c_approvata_sel, "", $htmlPage);
            break;
        case 'Approvata':
            $htmlPage = str_replace($c_sospesa_sel, "", $htmlPage);
            $htmlPage = str_replace($c_approvata_sel, "selected", $htmlPage);
            break;
    }

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if ($connectionOk) {
        $candidature = $connection->getCandidatureAndEmail($filter_candidatura);
        $connection->closeConnection();
        if ($candidature != null) {
            $list .= "<ul id='acnSospese'>";
            foreach ($candidature as $index => $candidatura) {
                $list .= file_get_contents("template/templateCandidaturaAdmin.html");

                $list = str_replace("collapse", "collapse" . $index, $list); // candidatura class

                $list = str_replace("pTitolo", Sanitizer::forHtml($candidatura["nome"]), $list);
                $list = str_replace("percorsoimmagine", FS::findImage($candidatura["nome"]), $list);
                $list = str_replace("descrizioneimmagine", Sanitizer::forHtml($candidatura["alt"]), $list);

                $list = str_replace("candidaturaAlt", "candidaturaAlt" . $index , $list); // alt input id
                $list = str_replace("pValue", !empty($candidatura["alt"]) ?"value='" . Sanitizer::forHtml($candidatura["alt"]). "'" : "", $list);

                $list = str_replace("pDurata", Sanitizer::forHtml($candidatura["durata"] . "'"), $list);
                $list = str_replace("pAnno", Sanitizer::forHtml($candidatura["anno"]), $list);
                $list = str_replace("pRegista", Sanitizer::forHtml($candidatura["regista"]), $list);
                $list = str_replace("pProduttore", Sanitizer::forHtml($candidatura["produttore"]), $list);
                $list = str_replace("pCast", Sanitizer::forHtml($candidatura["cast"]), $list);
                $list = str_replace("pEmail", Sanitizer::forHtml($candidatura["email"]), $list);
                $list = str_replace("pDescrizione", Sanitizer::forHtml($candidatura["descrizione"]), $list);

                if ($filter_candidatura == "Approvata") {
                    $list = str_replace("rifiutaCand", "style=\"display:none;\"", $list);
                    $list = str_replace("accettaCand", "style=\"display:none;\"", $list);
                    $list = str_replace("none;\" sospendiCand", "show;\"", $list);
                    $list = str_replace("pDisabled", "disabled", $list);
                } elseif ($filter_candidatura == "Sospesa"){
                    $list = str_replace("pDisabled", "", $list);
                }
            }
            unset($index);
            unset($candidatura);
            $list .= "</ul>";
        } else {
            $list = "<p>Non sono presenti candidature</p>";
        }
    } else {
        $list = "<p>Errore connessione db</p>";
    }
    if ($filter_candidatura == "Approvata") {
        $htmlPage = str_replace("in sospeso</h1>", "approvate</h1>", $htmlPage);
        $htmlPage = str_replace("alertRifiuto", "style=\"display:none;\"", $htmlPage);
    } else {
        $htmlPage = str_replace("alertSospensione", "style=\"display:none;\"", $htmlPage);
    }
    $htmlPage = str_replace("<listaCandidature/>", $list, $htmlPage);
}

/**
 * Rejects (deletes) candidatura from candidature in sospeso
 * @request POST
 */
function rifiutaCandidatura()
{
    if (!Login::is_logged_admin()) return;

    $_SESSION["method"] = "Rifiuta candidatura";
    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if ($connectionOk) {
        if ($connection->deleteCandidatura($_POST["titolo"])) {
            $feedback = "Candidatura: \"" . $_POST["titolo"] . "\" rifiutata con successo";
            $_SESSION["success"] = true;
        } else {
            $feedback = "Errore nella modifica";
            $_SESSION["success"] = false;
        }
        $connection->closeConnection();
    } else {
        $feedback = "Problemi di connessione al DB";
        $_SESSION["success"] = false;
    }
    $_SESSION["feedback"] = $feedback;
}

/**
 * Approves candidatura from candidature in sospeso
 * @request POST
 */
function approvaCandidatura()
{
    if (!Login::is_logged_admin()) return;

    $_SESSION["method"] = "Accetta candidatura";
    $titolo = $_POST["titolo"];
    $alt = $_POST["alt"];

    if (Utils::validate($alt, Utils::altRegex)) {

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            if ($connection->approvaCandidatura($titolo, $alt)) {
                $feedback = "Candidatura: \"" . $titolo . "\" approvata con successo";
                $_SESSION["success"] = true;
            } else {
                $feedback = "Errore nell'inserimento";
                $_SESSION["success"] = false;
            }
            $connection->closeConnection();
        } else {
            $feedback = "Problemi di connessione al DB";
            $_SESSION["success"] = false;
        }

    } else {
        $feedback = "Errore nella compliazione";
        $_SESSION["success"] = false;
    }
    $_SESSION["feedback"] = $feedback;
}

/**
 * Suspends (goes back to "to accept/reject state") candidatura from candidature in sospeso
 * Also delets all proiezioni of the selected candidatura
 * @request POST
 */
function sospendiCandidatura()
{
    if (!Login::is_logged_admin()) return;

    $_SESSION["method"] = "Sospendi candidatura";
    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if ($connectionOk) {
        if ($connection->sospendiCandidatura($_POST["titolo"])
            && $connection->deleteProizioniOnSuspend($_POST["titolo"])) {
            $feedback = "Candidatura: \"" . $_POST["titolo"] . "\" sospesa con successo";
            $_SESSION["success"] = true;
        } else {
            $feedback = "Errore nella modifica";
            $_SESSION["success"] = false;
        }
        $connection->closeConnection();
    } else {
        $feedback = "Problemi di connessione al DB";
        $_SESSION["success"] = false;
    }
    $_SESSION["feedback"] = $feedback;
}

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    switch ($_POST["method"]) {
        case "Accetta candidatura":
            approvaCandidatura();
            break;
        case "Rifiuta candidatura":
            rifiutaCandidatura();
            break;
        case "Sospendi candidatura":
            sospendiCandidatura();
            break;
    }


    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/adminCandidature.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);

    // print db content
    printCandidature($htmlPage);

    // feedback
    if (isset($_SESSION["method"])
        && isset($_SESSION["success"])) {
        if ($_SESSION["method"] == "Accetta candidatura"
            || $_SESSION["method"] == "Rifiuta candidatura"
            || $_SESSION["method"] == "Sospendi candidatura")
            Utils::printFeedback($htmlPage, "<feedbackCandidature/>");

        unset($_SESSION["method"]);
        unset($_SESSION["feedback"]);
        unset($_SESSION["success"]);
    }

    echo $htmlPage;
}