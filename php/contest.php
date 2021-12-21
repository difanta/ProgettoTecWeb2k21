<?php

session_start();

include "login.php";

use DB\DBAccess;

function submitContest(&$htmlPage)
{
    $messaggi = "";

    if (Login::is_logged()) {

        if (isset($_POST["submit"])) {
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
                $messaggi = "<li>Titolo non present<li/>";
            } elseif (preg_match('/\d/', $titolo)) {
                $messaggi = "<li>Nome non può contenere numeri<li/>";
            }

            if (strlen($descrizione) == 0) {
                $messaggi = "<li>Descrizione non presente<li/>";
            }

            if (strlen($linkyt) == 0) {
                $messaggi = "<li>Link yt non presente<li/>";
            }

            if (strlen($email) == 0) {
                $messaggi = "<li>Email non presente<li/>";
            }

            if (strlen($durata) == 0) {
                $messaggi = "<li>Durata non presente<li/>";
            }

            if (strlen($anno) == 0) {
                $messaggi = "<li>Anno non presente<li/>";
            } elseif (preg_match('/^([SW])\w+([0-9]{4})$/', $anno)) {
                $messaggi = "<li>Anno può essere solo un numero di 4 cifre<li/>";
            }

            if (strlen($regista) == 0) {
                $messaggi = "<li>Resgista non presente<li/>";
            } elseif (preg_match('/\d/', $regista)) {
                $messaggi = "<li>Regista non può contenere numeri<li/>";
            }

            if (strlen($produttore) == 0) {
                $messaggi = "<li>Produttore non presente<li/>";
            } elseif (preg_match('/\d/', $produttore)) {
                $messaggi = "<li>Produttore non può contenere numeri<li/>";
            }

            if (strlen($cast) == 0) {
                $messaggi = "<li>Cast non presente<li/>";
            } elseif (preg_match('/\d/', $cast)) {
                $messaggi = "<li>Cast non può contenere numeri<li/>";
            }

            if ($messaggi == "") {

                $connection = new DBAccess();
                $connectionOk = $connection->openDB();

                if ($connectionOk) {
                    if ($connection->insert("insert into Film(id ,nome, descrizione, durata, anno, regista, produttore, cast, in_gara, approvato, candidatore) 
                                          values (DEFAULT, '$titolo','$descrizione','$durata','$anno', '$regista', '$produttore', '$cast', 1, 0," . $_SESSION["login"] . ");")) {
                        $messaggi = "<p>Film aggiunto con successo<p/>";
                    } else {
                        $messaggi = "<p>Errore nell aggiunta<p/>";
                    }
                    $connection->closeConnection();
                } else {
                    $messaggi = "<li>problemi db<li/>";
                }
            } else {
                $messaggi = "<ul>" . $messaggi . "<ul/>";
            }
        }
    } else { // not logged
        $messaggi .= "<li>Utente non loggato<li/>";
    }

    $htmlPage = str_replace("<messaggi/>", $messaggi, $htmlPage);
}

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handle_login();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header("HTTP/1.1 303 See Other");
    header("Location: ./contest.php");
} else /* GET */ {
    $htmlPage = file_get_contents("../HTML/contest.html");

    // show login/register/logout results
    Login::set_login_contents($htmlPage);
    submitContest($htmlPage);

    $htmlPage = str_replace("placeholder", "style", $htmlPage);

    echo $htmlPage;
}
