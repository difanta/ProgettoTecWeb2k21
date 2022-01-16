<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

/**
 * Submits contest
 * @request POST
 */
function submitContest()
{
    $_SESSION["method"] = "Invia Candidatura";
    $titolo = $_POST["titolo"];
    $descrizione = $_POST["descrizione"];
    $durata = $_POST["durata"];
    $anno = $_POST["anno"];
    $regista = $_POST["regista"];
    $produttore = $_POST["produttore"];
    $cast = $_POST["cast"];

    if (Utils::validate($titolo, "/[\wàèéìòù]{1,}/")
        && Utils::validate($descrizione, "/^.{10,}$/")
        && Utils::validate($durata, "/^[6-9][0-9]|1[0-7][0-9]|180$/")
        && Utils::validate($anno, "/^19[0-9][0-9]|20[0-1][0-9]|202[0-2]$/")
        && Utils::validate($regista, "/[a-zA-Zàèéìòù]{1,}/")
        && Utils::validate($produttore, "/[a-zA-Zàèéìòù]{1,}/")
        && Utils::validate($cast, "/^.{5,}$/")) {

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            if ($connection->insertContestFilm($titolo, $descrizione, $durata, $anno, $regista, $produttore, $cast)) {
                $feedback = "Candidatura proposta con successo";
                $_SESSION["success"] = true;
            } else {
                $feedback = "Errore nell' operazione";
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

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();
    if (Login::is_logged()) {
        if ($_POST["method"] == "Invia Candidatura") {
            submitContest();
        }
    } else {
        $_SESSION["success"] = false;
    }
    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/contest.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);

    if (isset($_SESSION["method"])
        && isset($_SESSION["success"])) {
        if ($_SESSION["method"] == "Invia Candidatura") {
            Utils::printFeedback($htmlPage, "<feedbackCandidatura/>");
        }
        unset($_SESSION["method"]);
        unset($_SESSION["feedback"]);
        unset($_SESSION["success"]);
    }
    echo $htmlPage;
}
