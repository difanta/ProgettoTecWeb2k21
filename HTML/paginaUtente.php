<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function printInfoUtente(&$htmlPage)
{
    $form = "";

    if (Login::is_logged()) {

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            $results = $connection->getUserById();
            $connection->closeConnection();
            if ($results != null) {

                $form = file_get_contents("template/formInfoUtente.html");

                $form = str_replace("pNome", $results[0]["nome"], $form);
                $form = str_replace("pCognome", $results[0]["cognome"], $form);
                $form = str_replace("pDataDiNascita", $results[0]["data_di_nascita"], $form);
                $form = str_replace("pEmail", $results[0]["email"], $form);
                $form = str_replace("pPassword", $results[0]["password"], $form);

            } else {
                $form .= "<p>Errore load Info</p>";
            }
        } else {
            $form .= "<p>Errore connessione db</p>";
        }
    } else { // not logged
        $form .= "<p>Utente non loggato</p>";
    }

    $htmlPage = str_replace("<utenteForm/>", $form, $htmlPage);
}

function updateInfoUtente()
{
    $messaggi = "";
    if (Login::is_logged()) {
        if ($_POST["method"] == "Invia") {
            $nome = $_POST["nome"];
            $cognome = $_POST["cognome"];
            $dataNascita = $_POST["dataNascita"];
            $email = $_POST["email"];
            $password = $_POST["password"];


            if (strlen($nome) == 0) {
                $messaggi .= "<li>Nome non present</li>";
            } elseif (preg_match('/\d/', $nome)) {
                $messaggi .= "<li>Nome non può contenere numeri</li>";
            }

            if (strlen($cognome) == 0) {
                $messaggi .= "<li>Cognome non presente</li>";
            } elseif (preg_match('/\d/', $cognome)) {
                $messaggi .= "<li>Cognome non può contenere numeri</li>";
            }

            if (strlen($dataNascita) == 0) { // add controlli
                $messaggi .= "<li>Data di nascita non presente</li>";
            }

            if (strlen($email) == 0) {
                $messaggi .= "<li>Email non presente</li>";
            }

            if (strlen($password) == 0) {
                $messaggi .= "<li>Password non presente</li>";
            }

            if ($messaggi == "") {

                $connection = new DBAccess();
                $connectionOk = $connection->openDB();

                if ($connectionOk) {
                    if ($connection->updateUser($nome, $cognome, $dataNascita, $email, $password)) {
                        $messaggi .= "<p>Utente modificato con successo<p/>";
                        $_SESSION["success"] = true;
                    } else {
                        $messaggi .= "<p>Errore nella modifica<p/>";
                        $_SESSION["success"] = false;
                    }
                    $connection->closeConnection();
                } else {
                    $messaggi .= "<li>problemi db</li>";
                    $_SESSION["success"] = false;
                }
            } else {
                $messaggi = "<ul>" . $messaggi . "</ul>";
                $_SESSION["success"] = true;
            }
        }
    } else { // not logged
        $messaggi = "<ul>" . $messaggi . "</ul>";
        $messaggi .= "<ul><li>Utente non loggato</li></ul>";
        $_SESSION["success"] = false;
    }
    $_SESSION["messaggi"] = $messaggi;
}

function printUpdateInfoUtente(&$htmlPage)
{
    if (isset($_SESSION["method"]) && $_SESSION["method"] == "Invia") {

        $messaggi = isset($_SESSION["messaggi"]) ? $_SESSION["messaggi"] : "";
        $htmlPage = str_replace("<messaggi/>", $messaggi, $htmlPage);

        unset($_SESSION["method"]);
        unset($_SESSION["success"]);
    }
}

function deleteInfoUtente()
{
    if (Login::is_logged()) {
        if ($_POST["method"] == "Elimina Account") {
            $connection = new DBAccess();
            $connectionOk = $connection->openDB();

            if ($connectionOk) {
                if ($connection->deleteUser()) {
                    session_unset();
                    session_destroy();
                    session_start();
                    $_SESSION["success"] = true;
                } else {
                    $_SESSION["success"] = false;
                }
                $connection->closeConnection();
            } else {
                $_SESSION["success"] = false;
            }
        }
    } else { // not logged
        $_SESSION["success"] = false;
    }
}

function printBiglietti(&$htmlPage)
{
    $listaBiglietti = "";

    if (Login::is_logged()) {

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            $biglietti = $connection->getUserTickets();
            $connection->closeConnection();
            if ($biglietti != null) {
                $listaBiglietti .= "<ul>";
                foreach ($biglietti as $biglietto) {
                    $listaBiglietti .= file_get_contents("template/templateTicketUtente.html");

                    $listaBiglietti = str_replace("pNome", $biglietto["nome"], $listaBiglietti);
                    $listaBiglietti = str_replace("pId", $biglietto["id"], $listaBiglietti);
                    $listaBiglietti = str_replace("pOrario", $biglietto["data"] . " " . $biglietto["ora"], $listaBiglietti);
                }
                unset($biglietto);
                $listaBiglietti .= "</ul>";
            } else {
                $listaBiglietti .= "<p>Non sono presenti biglietti</p>";
            }
        } else {
            $listaBiglietti .= "<p>Errore connessione db</p>";
        }
    } else { // not logged
        $listaBiglietti .= "<p>Utente non loggato</p>";
    }

    $htmlPage = str_replace("<listaBiglietti/>", $listaBiglietti, $htmlPage);
}

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();
    updateInfoUtente();
    if ($_POST["method"] == "Elimina Account") {
        deleteInfoUtente();
        header("Location: index.php");
        die();
    }

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/paginaUtente.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printInfoUtente($htmlPage);
    printBiglietti($htmlPage);
    printUpdateInfoUtente($htmlPage);

    echo $htmlPage;
}

