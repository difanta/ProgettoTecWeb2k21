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
    $email = $_POST["email"];
    $durata = $_POST["durata"];
    $anno = $_POST["anno"];
    $regista = $_POST["regista"];
    $produttore = $_POST["produttore"];
    $cast = $_POST["cast"];

    /*
                if (strlen($titolo) == 0) {
                    $feedback = "<li>Titolo non present</li>";
                } elseif (preg_match('/\d/', $titolo)) {
                    $feedback = "<li>Nome non può contenere numeri</li>";
                }

                if (strlen($descrizione) == 0) {
                    $feedback = "<li>Descrizione non presente</li>";
                }

                if (strlen($email) == 0) {
                    $feedback = "<li>Email non presente</li>";
                }

                if (strlen($durata) == 0) {
                    $feedback = "<li>Durata non presente</li>";
                }

                if (strlen($anno) == 0) {
                    $feedback = "<li>Anno non presente</li>";
                } elseif (preg_match('/^([SW])\w+([0-9]{4})$/', $anno)) {
                    $feedback = "<li>Anno può essere solo un numero di 4 cifre</li>";
                }

                if (strlen($regista) == 0) {
                    $feedback = "<li>Resgista non presente</li>";
                } elseif (preg_match('/\d/', $regista)) {
                    $feedback = "<li>Regista non può contenere numeri</li>";
                }

                if (strlen($produttore) == 0) {
                    $feedback = "<li>Produttore non presente</li>";
                } elseif (preg_match('/\d/', $produttore)) {
                    $feedback = "<li>Produttore non può contenere numeri</li>";
                }

                if (strlen($cast) == 0) {
                    $feedback = "<li>Cast non presente</li>";
                } elseif (preg_match('/\d/', $cast)) {
                    $feedback = "<li>Cast non può contenere numeri</li>";
                }
    */
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
