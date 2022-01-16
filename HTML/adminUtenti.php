<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function eliminaUtente() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"]) && $_POST["method"] == "Elimina Utente") {
        $_SESSION["method"] = $_POST["method"];
        $_SESSION["success"] = false;

        if(!isset($_GET["username"])) { return; }
        $email = $_GET["username"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        if($connectionOk) {
            if($connection->deleteUserByEmail($email)) {
                $_SESSION["success"] = true;
                $_SESSION["feedback"] = "Utente eliminato con successo!";
            } else {
                $_SESSION["success"] = false;
                $_SESSION["feedback"] = "Utente non trovato :(";
            }
            $connection->closeConnection();
        } else {
            $_SESSION["success"] = false;
            $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
        }
    }
}

function resetPassword() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"]) && $_POST["method"] == "Reset Password") {
        $_SESSION["method"] = $_POST["method"];
        $_SESSION["success"] = false;

        if(!isset($_GET["username"])) { return; }
        $email = $_GET["username"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
        
        if($connectionOk) {
            $_SESSION["success"] = true;
            $_SESSION["feedback"] = "Password resettata con successo!";
        } else {
            $_SESSION["success"] = false;
            $_SESSION["feedback"] = "Utente non trovato :(";
        }
        $connection->closeConnection();
    } else {
        $_SESSION["success"] = false;
        $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
    }
}

function modificaBiglietto() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"])) {
        if($_POST["method"] == "Modifica Biglietto") {
            $_SESSION["method"] = $_POST["method"];
            $_SESSION["success"] = false;

            if(!isset($_POST["mod_idBiglietto"])) { return; }
            $idBiglietto = $_POST["mod_idBiglietto"];
            if(!isset($_POST["mod_idProiezione"])) { return; }
            $idProiezione= $_POST["mod_idProiezione"];

            $connection = new DBAccess();
            $connectionOk = $connection->openDB();
        
            if($connectionOk) {
                if($connection->modifyTicket($idBiglietto, $idProiezione)) {
                    $_SESSION["success"] = true;
                    $_SESSION["feedback"] = "Biglietto modificato con successo!";
                } else {
                    $_SESSION["success"] = false;
                    $_SESSION["feedback"] = "Biglietto non trovato :(";
                }
                $connection->closeConnection();
            } else {
                $_SESSION["success"] = false;
                $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
            }
        } else if($_POST["method"] == "Elimina Biglietto") {
            $_SESSION["method"] = $_POST["method"];
            $_SESSION["success"] = false;

            if(!isset($_POST["mod_idBiglietto"])) { return; }
            $idBiglietto = $_POST["mod_idBiglietto"];

            $connection = new DBAccess();
            $connectionOk = $connection->openDB();
        
            if($connectionOk) {
                if($connection->deleteTicket($idBiglietto)) {
                    $_SESSION["success"] = true;
                    $_SESSION["feedback"] = "Biglietto eliminato con successo!";
                } else {
                    $_SESSION["success"] = false;
                    $_SESSION["feedback"] = "Biglietto non trovato :(";
                }
                $connection->closeConnection();
            } else {
                $_SESSION["success"] = false;
                $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
            }
        }
    }
}

function aggiungiBiglietto() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"]) && $_POST["method"] == "Aggiungi Biglietto") {
        $_SESSION["method"] = $_POST["method"];
        $_SESSION["success"] = false;        

        if(!isset($_GET["username"])) { return; }
        $email = $_GET["username"];
        if(!isset($_POST["agg_idProiezione"])) { return; }
        $idProiezione= $_POST["agg_idProiezione"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        if($connectionOk) {
            if($connection->insertTicketByEmail($email, $idProiezione)) {
                $_SESSION["success"] = true;
                $_SESSION["feedback"] = "Biglietto aggiunto con successo!";
            } else {
                $_SESSION["success"] = false;
                $_SESSION["feedback"] = "Errore nell'aggiunta del biglietto :("; // non succede mai in realtà
            }
            $connection->closeConnection();
        } else {
            $_SESSION["success"] = false;
            $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
        }
    }
}

function printUtentiAndFilms(&$htmlPage) {
    $p_utenti = "<utenti/>";
    $p_nomifilm = "<nomifilm/>";

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $utenti = $connection->getEmailUtenti();
        $films = $connection->getNomiFilmApprovati();
        $connection->closeConnection();

        $template = "<option value=\"utente\">utente</option>";
        $stringa = "";
        foreach($utenti as $utente) {
            $stringa .= str_replace("utente", Sanitizer::forHtml($utente["email"]), $template);
        }
        $htmlPage = str_replace($p_utenti, $stringa, $htmlPage);

        $template = "<option value=\"nomefilm\">nomefilm</option>";
        $stringa = "";
        foreach($films as $film) {
            $stringa .= str_replace("nomefilm", Sanitizer::forHtml($film["nome"]), $template);
        }
        $htmlPage = str_replace($p_nomifilm, $stringa, $htmlPage);
    } else {
        $htmlPage = str_replace($p_utenti, "", $htmlPage);
        $htmlPage = str_replace($p_nomifilm, "", $htmlPage);
    }
}

function printUtenteAndBiglietti(&$htmlPage) {
    if(!Login::is_logged_admin()) { return; }

    $p_biglietto = "<biglietto/>";

    if(isset($_SESSION["method"]) && ($_SESSION["method"] == "Modifica Biglietto" || $_SESSION["method"] == "Elimina Biglietto" || $_SESSION["method"] == "Elimina Utente" || $_SESSION["method"] == "Reset Password" || $_SESSION["method"] == "Aggiungi Biglietto")) {
        Utils::printFeedback($htmlPage, "<feedback/>");
        unset($_SESSION['method']);
        unset($_SESSION['success']);
    }

    if(!isset($_GET["username"])) { 
        Login::hideElement("<ifutenteselezionato>", "</ifutenteselezionato>", $htmlPage);
        $htmlPage = str_replace("userselected", "", $htmlPage);
        return; 
    }

    $email = $_GET["username"];

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $template = file_get_contents("./template/templateAdminUtentiBiglietto.html");
        $stringaBiglietti = "";
        
        $results_user = $connection->getUserByEmail($email);
        $results_tickets = $connection->getUserTicketsByEmail($email);
        $connection->closeConnection();

        if($results_user && $results_user[0]) {
            $ticketTemplate = file_get_contents("./template/templateAdminUtentiBiglietto.html");

            if($results_tickets) {
                foreach($results_tickets as $ticket) {
                    $biglietto_html = str_replace("idbiglietto"                , Sanitizer::forHtml($ticket["id"])  , $ticketTemplate);
                    $biglietto_html = str_replace("mod_nomefilmselezionato"    , Sanitizer::forHtml($ticket["nome"]), $biglietto_html);
                    $biglietto_html = str_replace("mod_idproiezioneselezionata", Sanitizer::forHtml($ticket["pid"]) , $biglietto_html);
                    $htmlPage = str_replace($p_biglietto, $biglietto_html . $p_biglietto, $htmlPage);
                }
            }
            $htmlPage = str_replace("emailutente", Sanitizer::forHtml($results_user[0]["email"])          , $htmlPage);
            $htmlPage = str_replace("nomeutente" , Sanitizer::forHtml($results_user[0]["nome"])           , $htmlPage);
            $htmlPage = str_replace("cognome"    , Sanitizer::forHtml($results_user[0]["cognome"])        , $htmlPage);
            $htmlPage = str_replace("datanascita", Sanitizer::forHtml($results_user[0]["data_di_nascita"]), $htmlPage);
        }
    }

    Login::showElement("<ifutenteselezionato>", "</ifutenteselezionato>", $htmlPage);
    $htmlPage = str_replace("userselected", Sanitizer::forHtml($email), $htmlPage);
    $htmlPage = str_replace($p_biglietto, "", $htmlPage);
}

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();
    eliminaUtente();
    resetPassword();
    modificaBiglietto();
    aggiungiBiglietto();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    if(isset($_SESSION["method"]) && $_SESSION["method"] == "Elimina Utente" && $_SESSION["success"] == true) {
        header("Location: " . "./adminUtenti.php");
    } else {
        header("Location: " . $_SERVER["REQUEST_URI"]);
    }
} else /* GET */ {
    $htmlPage = file_get_contents("template/adminUtenti.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printUtenteAndBiglietti($htmlPage);
    printUtentiAndFilms($htmlPage);

    echo $htmlPage;
}