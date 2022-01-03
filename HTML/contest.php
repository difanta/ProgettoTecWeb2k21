<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function submitContest()
{
    $messaggi = "";

    if (Login::is_logged()) {

        if ($_POST["method"] == "Invia Candidatura") {
            $titolo = $_POST["titolo"];
            $descrizione = $_POST["descrizione"];
            $linkyt = $_POST["linkyt"];
            $email = $_POST["email"];
            $durata = $_POST["durata"];
            $anno = $_POST["anno"];
            $regista = $_POST["regista"];
            $produttore = $_POST["produttore"];
            $cast = $_POST["cast"];


            if (strlen($titolo) == 0) {
                $messaggi = "<li>Titolo non present</li>";
            } elseif (preg_match('/\d/', $titolo)) {
                $messaggi = "<li>Nome non può contenere numeri</li>";
            }

            if (strlen($descrizione) == 0) {
                $messaggi = "<li>Descrizione non presente</li>";
            }

            if (strlen($linkyt) == 0) {
                $messaggi = "<li>Link yt non presente</li>";
            }

            if (strlen($email) == 0) {
                $messaggi = "<li>Email non presente</li>";
            }

            if (strlen($durata) == 0) {
                $messaggi = "<li>Durata non presente</li>";
            }

            if (strlen($anno) == 0) {
                $messaggi = "<li>Anno non presente</li>";
            } elseif (preg_match('/^([SW])\w+([0-9]{4})$/', $anno)) {
                $messaggi = "<li>Anno può essere solo un numero di 4 cifre</li>";
            }

            if (strlen($regista) == 0) {
                $messaggi = "<li>Resgista non presente</li>";
            } elseif (preg_match('/\d/', $regista)) {
                $messaggi = "<li>Regista non può contenere numeri</li>";
            }

            if (strlen($produttore) == 0) {
                $messaggi = "<li>Produttore non presente</li>";
            } elseif (preg_match('/\d/', $produttore)) {
                $messaggi = "<li>Produttore non può contenere numeri</li>";
            }

            if (strlen($cast) == 0) {
                $messaggi = "<li>Cast non presente</li>";
            } elseif (preg_match('/\d/', $cast)) {
                $messaggi = "<li>Cast non può contenere numeri</li>";
            }

            if ($messaggi == "") {

                $connection = new DBAccess();
                $connectionOk = $connection->openDB();

                if ($connectionOk) {
                    if ($connection->insertContestFilm($titolo, $descrizione, $durata, $anno, $regista, $produttore, $cast)) {
                        $messaggi = "<p>Film aggiunto con successo</p>";
                        $_SESSION["success"] = true;
                    } else {
                        $messaggi = "<p>Errore nell aggiunta</p>";
                        $_SESSION["success"] = false;
                    }
                    $connection->closeConnection();
                } else {
                    $messaggi = "<li>problemi db</li>";
                    $_SESSION["success"] = false;
                }
            } else {
                $messaggi = "<ul>" . $messaggi . "</ul>";
                $_SESSION["success"] = false;
            }
        }
    } else { // not logged
        $messaggi .= "<li>Utente non loggato</li>";
        $_SESSION["success"] = false;
    }
    $_SESSION["messaggi"] = $messaggi;

}

function printSubmitContest(&$htmlPage)
{
    if (isset($_SESSION["method"]) && $_SESSION["method"] == "Invia Candidatura") {
        echo 'printupdate dentro';

        $messaggi = isset($_SESSION["messaggi"]) ? $_SESSION["messaggi"] : " ";
        $htmlPage = str_replace("<messaggi/>", $messaggi, $htmlPage);

        unset($_SESSION["method"]);
        unset($_SESSION["success"]);
    }
}

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();
    submitContest();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header($_SERVER["SERVER_PROTOCOL"]." 303 See Other");
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/contest.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printSubmitContest($htmlPage);

    echo $htmlPage;
}
