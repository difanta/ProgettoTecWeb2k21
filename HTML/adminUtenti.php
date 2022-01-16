<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function eliminaUtente() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"]) && $_POST["method"] == "Elimina Utente") {

        if(!isset($_POST["username"])) { return; }
        $email = $_POST["username"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        if($connectionOk) {
            $_SESSION["success"] = $connection->deleteUserByEmail($email);
        } else {
            $_SESSION["success"] = false;
        }
        $_SESSION["method"] = $_POST["method"];
    }
}

function resetPassword() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"]) && $_POST["method"] == "Reset Password") {

        if(!isset($_POST["username"])) { return; }
        $username = $_POST["username"];
        $nomeFilm = $_POST["agg_nomeFilm"];
        $idProiezione= $_POST["agg_idProiezione"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        if($connectionOk) {
            $_SESSION["success"] = $connection->insertTicket();
        } else {
            $_SESSION["success"] = false;
        }
        $_SESSION["method"] = $_POST["method"];
    }
}

function modificaBiglietto() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"])) {
        if($_POST["method"] == "Modifica Biglietto") {
            
            if(!isset($_POST["mod_idBiglietto"])) { return; }
            $idBiglietto = $_POST["mod_idBiglietto"];
            $nomeFilm = $_POST["mod_nomeFilm"];
            $idProiezione= $_POST["mod_idProiezione"];

            $connection = new DBAccess();
            $connectionOk = $connection->openDB();
        
            if($connectionOk) {
                $_SESSION["success"] = $connection->modifyTicket($idBiglietto, $idProiezione);
            } else {
                $_SESSION["success"] = false;
            }
            $_SESSION["method"] = $_POST["method"];
        } else if($_POST["method"] == "Elimina Biglietto") {
            
            if(!isset($_POST["username"])) { return; }
            $email = $_POST["username"];

            $connection = new DBAccess();
            $connectionOk = $connection->openDB();
        
            if($connectionOk) {
                $_SESSION["success"] = $connection->deleteTicket($idBiglietto);
            } else {
                $_SESSION["success"] = false;
            }
            $_SESSION["method"] = $_POST["method"];
        }
    }
}

function aggiungiBiglietto() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"]) && $_POST["method"] == "Aggiungi Biglietto") {
                
        if(!isset($_POST["username"])) { return; }
        $email = $_POST["username"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        if($connectionOk) {
            $_SESSION["success"] = true;
            //$_SESSION["success"] = $connection->deleteUserByEmail($email);
        } else {
            $_SESSION["success"] = false;
        }
        $_SESSION["method"] = $_POST["method"];
    }
}

function printUtentiAndFilms(&$htmlPage) {
    $p_utenti = "<utenti/>";
    $p_nomifilm = "<nomifilm/>";

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $template = "<option value=\"utente\">utente</option>";
        $stringa = "";
        $utenti = $connection->getEmailUtenti();
        foreach($utenti as $utente) {
            $stringa .= str_replace("utente", Sanitizer::forHtml($utente["email"]), $template);
        }
        $htmlPage = str_replace($p_utenti, $stringa, $htmlPage);

        $template = "<option value=\"nomefilm\">nomefilm</option>";
        $stringa = "";
        $films = $connection->getNomiFilmApprovati();
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

    if(isset($_SESSION["method"]) && ($_SESSION["method"] == "Modifica Biglietto" || $_SESSION["method"] == "Elimina Biglietto" || $_SESSION["method"] == "Elimina Utente" || $_SESSION["method"] == "Reset Password")) {
        echo $_SESSION['method'] . " success: " . $_SESSION['success'];
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

function printAggiungiBiglietto(&$htmlPage) {
    if(isset($_SESSION["method"]) && $_SESSION["method"] == "Aggiungi Biglietto") {
        echo $_SESSION['method'] . " success: " . $_SESSION['success'];
        unset($_SESSION['method']);
        unset($_SESSION['success']);
    }

    $htmlPage = str_replace("agg_nomefilmselezionato", "", $htmlPage);
    $htmlPage = str_replace("agg_idproiezioneselezionata", "", $htmlPage);
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
    printAggiungiBiglietto($htmlPage);

    echo $htmlPage;
}