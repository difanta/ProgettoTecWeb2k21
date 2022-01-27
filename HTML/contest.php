<?php

session_start();

include "../php/login.php";
include_once "../php/fs.php";

use DB\DBAccess;

/**
 * Submits contest
 * @request POST
 */
function submitContest()
{
    if (!Login::is_logged()) return;

    $titolo = $_POST["titolo"];
    $descrizione = $_POST["descrizione"];
    $durata = $_POST["durata"];
    $anno = $_POST["anno"];
    $regista = $_POST["regista"];
    $produttore = $_POST["produttore"];
    $cast = $_POST["cast"];
    $imageTmpPath = $_FILES["imgFilm"]["tmp_name"];
    $imageExt = strtolower(pathinfo($_FILES["imgFilm"]["name"], PATHINFO_EXTENSION));

    if (Utils::validate($titolo, "/[\wàèéìòù]{1,}/")
        && Utils::validate($descrizione, "/^.{10,}$/")
        && Utils::validate($durata, "/^[6-9][0-9]|1[0-7][0-9]|180$/")
        && Utils::validate($anno, "/^19[0-9][0-9]|20[0-1][0-9]|202[0-2]$/")
        && Utils::validate($regista, "/[a-zA-Zàèéìòù]{1,}/")
        && Utils::validate($produttore, "/[a-zA-Zàèéìòù]{1,}/")
        && Utils::validate($cast, "/^.{5,}$/")
        && getimagesize($imageTmpPath)) {

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            if ($connection->insertContestFilm($titolo, $descrizione, $durata, $anno, $regista, $produttore, $cast)) {
                if (FS::moveImage($titolo, $imageTmpPath, $imageExt, $connection)) {
                    $feedback = "Candidatura proposta con successo";
                } else {
                    $feedback = "Candidatura proposta con successo, ci sono stati problemi imprevisti con la gestione dell'immagine, prego contattare un admin.";
                }
                $_SESSION["success"] = true;
            } else {
                $feedback = "Titolo già in uso :( Provare con un altro";
                $_SESSION["success"] = false;
            }
            $connection->closeConnection();
        } else {
            $feedback = "Problemi di connessione al DB";
            $_SESSION["success"] = false;
        }
    } else {
        $feedback = "Errore nella compilazione della form, per maggiori informazioni attivare javascript nel browser";
        $_SESSION["success"] = false;
    }
    $_SESSION["method"] = "Invia Candidatura";
    $_SESSION["feedback"] = $feedback;
    $_SESSION["titolo"] = $titolo;
    $_SESSION["descrizione"] = $descrizione;
    $_SESSION["durata"] = $durata;
    $_SESSION["anno"] = $anno;
    $_SESSION["regista"] = $regista;
    $_SESSION["produttore"] = $produttore;
    $_SESSION["cast"] = $cast;
}

/**
 * prints submitContest results, maintains input
 * @request GET
 */
function printSubmitContest(&$htmlPage)
{
    if (isset($_SESSION["method"])
        && isset($_SESSION["success"])
        && $_SESSION["method"] == "Invia Candidatura") {
        $htmlPage = str_replace("pTitolo", Sanitizer::forHtml($_SESSION["titolo"]), $htmlPage);
        $htmlPage = str_replace("pDescrizione", Sanitizer::forHtml($_SESSION["descrizione"]), $htmlPage);
        $htmlPage = str_replace("pDurata", Sanitizer::forHtml($_SESSION["durata"]), $htmlPage);
        $htmlPage = str_replace("pAnno", Sanitizer::forHtml($_SESSION["anno"]), $htmlPage);
        $htmlPage = str_replace("pRegista", Sanitizer::forHtml($_SESSION["regista"]), $htmlPage);
        $htmlPage = str_replace("pProduttore", Sanitizer::forHtml($_SESSION["produttore"]), $htmlPage);
        $htmlPage = str_replace("pCast", Sanitizer::forHtml($_SESSION["cast"]), $htmlPage);

        Utils::printFeedback($htmlPage, "<feedbackCandidatura/>");

        unset($_SESSION["titolo"]);
        unset($_SESSION["descrizione"]);
        unset($_SESSION["durata"]);
        unset($_SESSION["anno"]);
        unset($_SESSION["regista"]);
        unset($_SESSION["produttore"]);
        unset($_SESSION["cast"]);

        unset($_SESSION["method"]);
        unset($_SESSION["feedback"]);
        unset($_SESSION["success"]);
    } else {
        $htmlPage = str_replace("pTitolo", "", $htmlPage);
        $htmlPage = str_replace("pDescrizione", "", $htmlPage);
        $htmlPage = str_replace("pDurata", "", $htmlPage);
        $htmlPage = str_replace("pAnno", "", $htmlPage);
        $htmlPage = str_replace("pRegista", "", $htmlPage);
        $htmlPage = str_replace("pProduttore", "", $htmlPage);
        $htmlPage = str_replace("pCast", "", $htmlPage);
    }
}

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    if ($_POST["method"] == "Invia Candidatura") {
        submitContest();
    }

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/contest.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printSubmitContest($htmlPage);

    Utils::feedbackCleanUp($htmlPage, "<feedbackCandidatura/>");

    echo $htmlPage;
}
