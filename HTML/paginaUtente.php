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
            $results = $connection->get("SELECT * FROM  Utente where id = " . $_SESSION["login"]);
            $connection->closeConnection();
            if ($results != null) {
                $nome = $results[0]["nome"];
                $cognome = $results[0]["cognome"];
                $dataNascita = $results[0]["data_di_nascita"];
                $email = $results[0]["email"];
                $password = $results[0]["password"];

                $form = ">
                        </form>";

            } else {
                $form .= "<p>Errore load Info</p>";
            }
        } else {
            $form .= "<p>Errore connessione db</p>";
        }
    } else { // not logged
        $form .= "<p>Utente non loggato</p>";
    }

    $htmlPage = str_replace("<contestForm/>", $form, $htmlPage);
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
                $messaggi .= "<li>Nome non present<li/>";
            } elseif (preg_match('/\d/', $nome)) {
                $messaggi .= "<li>Nome non può contenere numeri<li/>";
            }

            if (strlen($cognome) == 0) {
                $messaggi .= "<li>Cognome non presente<li/>";
            } elseif (preg_match('/\d/', $cognome)) {
                $messaggi .= "<li>Cognome non può contenere numeri<li/>";
            }

            if (strlen($dataNascita) == 0) { // add controlli
                $messaggi .= "<li>Data di nascita non presente<li/>";
            }

            if (strlen($email) == 0) {
                $messaggi .= "<li>Email non presente<li/>";
            }

            if (strlen($password) == 0) {
                $messaggi .= "<li>Password non presente<li/>";
            }

            if ($messaggi == "") {

                $connection = new DBAccess();
                $connectionOk = $connection->openDB();

                if ($connectionOk) {
                    $id = $_SESSION["login"];
                    if ($connection->insert("update Utente 
                                            set nome ='$nome', cognome='$cognome', data_di_nascita='$dataNascita', email='$email', password='$password'
                                            where id='$id'")) {
                        $messaggi .= "<p>Utente modificato con successo<p/>";
                        $_SESSION["success"] = true;
                    } else {
                        $messaggi .= "<p>Errore nella modifica<p/>";
                        $_SESSION["success"] = false;
                    }
                    $connection->closeConnection();
                } else {
                    $messaggi .= "<li>problemi db<li/>";
                    $_SESSION["success"] = false;
                }
            } else {
                $messaggi = "<ul>" . $messaggi . "<ul/>";
                $_SESSION["success"] = true;
            }
        }
    } else { // not logged
        $messaggi .= "<li>Utente non loggato<li/>";
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
                $id = $_SESSION["login"];
                if ($connection->insert("delete from Utente where id='$id'")) {
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
        $utente = $_SESSION["login"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            $biglietti = $connection->get("SELECT Film.nome, Biglietto.id, orario 
                                            FROM Utente join Biglietto on Utente.id=Biglietto.utente
                                            join Proiezione on Proiezione.id=Biglietto.proiezione
                                            join Film on Film.id= Proiezione.film 
                                            where Utente.id='$utente'");
            $connection->closeConnection();
            if ($biglietti != null) {
                $listaBiglietti .= "<ul>";
                foreach ($biglietti as $biglietto) {
                    $listaBiglietti .=
                        "<li class=\"ticket\"><p><span class=\"ticket-up\">" . $biglietto["nome"] .
                        "</span><span class=\"ticket-up\">#" . $biglietto["id"] .
                        "</span></p><p><span class=\"ticket-low\">" . $biglietto["orario"] .
                        "</span></p></li>";
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
    if($_POST["method"] == "Elimina Account") {
        deleteInfoUtente();
        header("Location: home.php");
        die();
    }

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header("HTTP/1.1 303 See Other");
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

