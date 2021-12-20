<?php

session_start();

include "login.php";
use DB\DBAccess;

function submitContest(&$htmlPage)
{
    $messaggi = "";
    $titolo = "";
    $descrizione = "";
    $linkyt = "";
    $email = "";

    if (Login::is_logged()) {

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            if (isset($_POST["submit"])) {
                $titolo = $_POST["titolo"];
                $descrizione = $_POST["descrizione"];
                $linkyt = $_POST["linkyt"];
                $email = $_POST["email"];

                if (strlen($titolo) == 0) {
                    $messaggi .= "<li>Titolo non presente</li>";
                } elseif (preg_match('/\d/', $titolo)) {
                    $messaggi .= "<li>Il nome non può contenere numeri</li>";
                }

                if (strlen($descrizione) == 0) {
                    $messaggi .= "<li>Descrizione non presente</li>";
                }

                if (strlen($linkyt) == 0) {
                    $messaggi .= "<li>Descrizione non presente</li>";
                }

                if (strlen($email) == 0) {
                    $messaggi .= "<li>Descrizione non presente</li>";
                }

                if (strlen($messaggi) == "") {

                    $connection = new DBAccess();
                    $connectionOk = $connection->openDB();

                    if($connectionOk){ // TODO
                        $result = $connection->get("insert into Film(id, nome, descrizione, durata, anno, regista, produttore, cast, in_gara, approvato, candidatore) values (1, \"\");");
                    }

                    $messaggi .= "<p>Candidatura inviata con successo.</p>"
    }
            }
        } else {
            $messaggi .= "<p>Errore connessione db</p>";
        }
    } else { // not logged
        $messaggi .= "<p>Utente non loggato</p>";
    }

    $htmlPage =  str_replace("<listaBiglietti/>", $messaggi, $htmlPage);
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
    printBiglietti($htmlPage);

    $htmlPage = str_replace("placeholder", "style", $htmlPage);

    echo $htmlPage;
}
