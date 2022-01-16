<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

/**
 * Fills user info form with data from logged user
 * @request GET
 */
function printInfoUtente(&$htmlPage)
{
    $form = "";

    if (Login::is_logged()) {

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            $results = $connection->getUser();
            $connection->closeConnection();
            if ($results != null) {

                $form = file_get_contents("template/formInfoUtente.html");

                $form = str_replace("pNome", Sanitizer::forHtml($results[0]["nome"]), $form);
                $form = str_replace("pCognome", Sanitizer::forHtml($results[0]["cognome"]), $form);
                $form = str_replace("pDataDiNascita", Sanitizer::forHtml($results[0]["data_di_nascita"]), $form);
                $form = str_replace("pEmail", Sanitizer::forHtml($results[0]["email"]), $form);
                $form = str_replace("pPassword", Sanitizer::forHtml($results[0]["password"]), $form);

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

/**
 * Updates user's info on "modifica" > "Invia" buttons click
 * @request POST
 */
function updateInfoUtente()
{
    $_SESSION["method"] = "Invia";
    $nome = $_POST["nome"];
    $cognome = $_POST["cognome"];
    $dataNascita = $_POST["dataNascita"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (Utils::validate($nome, Utils::namesRegex)
        && Utils::validate($cognome, Utils::namesRegex)
        && Utils::validate($email, Utils::emailRegex)
        && Utils::validate($password, Utils::passwordRegex)) { // TODO add data di nascita?

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            if ($connection->updateUser($nome, $cognome, $dataNascita, $email, $password)) {
                $feedback = "Utente modificato con successo";
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

    } else {
        $feedback = "Errore nella compliazione";
        $_SESSION["success"] = false;
    }
    $_SESSION["feedback"] = $feedback;
}

/**
 * Deletes User on "elimina account" button click
 * @request POST
 */
function deleteInfoUtente()
{
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

/**
 * Replaces <listaBiglietti/> with ticket's list
 * @request GET
 */
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

                    $listaBiglietti = str_replace("pNome", Sanitizer::forHtml($biglietto["nome"]), $listaBiglietti);
                    $listaBiglietti = str_replace("pId", Sanitizer::forHtml($biglietto["id"]), $listaBiglietti);
                    $listaBiglietti = str_replace("pOrario", Sanitizer::forHtml($biglietto["data"] . " " . $biglietto["ora"]), $listaBiglietti);
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

/**
 * Replaces <listaCandidature/> with candidature's list
 * @request GET
 */
function printCandidature(&$htmlPage)
{
    $list = "";

    if (Login::is_logged()) {

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            $candidature = $connection->getUserCandidature();
            $connection->closeConnection();
            if ($candidature != null) {
                $list .= "<ul id='acnSospese'>";
                foreach ($candidature as $index => $candidatura) {
                    $list .= file_get_contents("template/templateCandidaturaUser.html");

                    $list = str_replace("collapse", "collapse" . $index, $list);
                    $list = str_replace("pTitolo", Sanitizer::forHtml($candidatura["nome"]), $list);
                    $list = str_replace("pDurata", Sanitizer::forHtml($candidatura["durata"] . "'"), $list);
                    $list = str_replace("pAnno", Sanitizer::forHtml($candidatura["anno"]), $list);
                    $list = str_replace("pRegista", Sanitizer::forHtml($candidatura["regista"]), $list);
                    $list = str_replace("pProduttore", Sanitizer::forHtml($candidatura["produttore"]), $list);
                    $list = str_replace("pCast", Sanitizer::forHtml($candidatura["cast"]), $list);
                    $list = str_replace("pEmail", Sanitizer::forHtml($candidatura["email"]), $list);
                    $list = str_replace("pDescrizione", Sanitizer::forHtml($candidatura["descrizione"]), $list);

                    if (!$candidatura["approvato"]) {
                        $list = str_replace("<pAction/>", "<input class='button' type='submit' name='method' value='Ritira candidatura'>", $list);
                    } else {
                        $list = str_replace("<pAction/>", "Approvata", $list);
                    }
                }
                unset($candidatura);
                $list .= "</ul>";
            } else {
                $list .= "<p>Non sono presenti candidature</p>";
            }
        } else {
            $list .= "<p>Errore connessione db</p>";
        }
    } else { // not logged
        $list .= "<p>Utente non loggato</p>";
    }

    $htmlPage = str_replace("<listaCandidature/>", $list, $htmlPage);
}

/**
 * deletes selected candidatura
 * @request POST
 */
function deleteCandidatura()
{
    $_SESSION["method"] = "Ritira candidatura";
    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if ($connectionOk) {
        if ($connection->deleteCandidatura($_POST["titolo"])) {
            $feedback = "Candidatura ritirata con successo";
            $_SESSION["success"] = true;
        } else {
            $feedback = "errori nell'operazione";
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

    // handle user triggered POSTs
    if (Login::is_logged()) {
        switch ($_POST["method"]) {
            case "Invia":
                updateInfoUtente();
                break;
            case "Ritira candidatura":
                deleteCandidatura();
                break;
            case "Elimina Account":
                deleteInfoUtente();
                http_response_code(303);
                header("Location: index.php");
                die();
        }
    } else {
        $_SESSION["success"] = false;
    }

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/paginaUtente.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);

    // print db content
    printInfoUtente($htmlPage);
    printBiglietti($htmlPage);
    printCandidature($htmlPage);

    // feedback
    if (isset($_SESSION["method"])
        && isset($_SESSION["success"])) {
        switch ($_SESSION["method"]) {
            case "Invia":
                Utils::printFeedback($htmlPage, "<feedbackInfoUtente/>");
                break;
            case "Ritira candidatura":
                Utils::printFeedback($htmlPage, "<feedbackDeleteCandidatura/>");
                break;
        }
        unset($_SESSION["method"]);
        unset($_SESSION["feedback"]);
        unset($_SESSION["success"]);
    }

    echo $htmlPage;
}

