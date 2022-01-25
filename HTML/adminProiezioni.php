<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function aggiungiProiezione() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"]) && $_POST["method"] == "Aggiungi Proiezione") {
        if(!isset($_POST["agg_data"])) { return; }
        $agg_nomeFilm = $_POST["agg_nomeFilm"];
        $agg_data = $_POST["agg_data"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        if($connectionOk) {
            if($connection->addProiezione($agg_nomeFilm, $agg_data)) {
                $_SESSION["success"] = true;
                $_SESSION["feedback"] = "Proiezione aggiunta con successo!";
            } else {
                $_SESSION["success"] = false;
                $_SESSION["feedback"] = "Errore, Proiezione non aggiunta"; // non succede mai in realtà
            }
            $connection->closeConnection();
        } else {
            $_SESSION["success"] = false;
            $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
        }
        $_SESSION["method"] = $_POST["method"];
        $_SESSION["agg_nomefilmselezionato"] = $agg_nomeFilm;
        $_SESSION["agg_dataselezionata"] = $agg_data;
    }
}

function modificaProiezione() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"]) && $_POST["method"] == "Modifica Proiezione") {
        $mod_IdProiezione  = $_POST["mod_idProiezione"]; 
        $mod_nomeFilm      = $_POST["mod_nomeFilm"];
        $mod_nomeNuovoFilm = $_POST["mod_nomeNuovoFilm"];
        $mod_nuovaData     = $_POST["mod_nuovaData"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        if($connectionOk) {
            if($connection->modifyProiezione($mod_IdProiezione, $mod_nomeNuovoFilm, $mod_nuovaData)) {
                $_SESSION["success"] = true;
                $_SESSION["feedback"] = "Proiezione modificata con successo!";
            } else {
                $_SESSION["success"] = false;
                $_SESSION["feedback"] = "Proiezione non trovata :(";
            }
            $connection->closeConnection();
        } else {
            $_SESSION["success"] = false;
            $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
        }
        $_SESSION["method"] = $_POST["method"];
        if($_SESSION["success"] == true) {
            if($mod_nomeFilm == $mod_nomeNuovoFilm) {
                $_SESSION["mod_idproiezioneselezionata"] = $mod_IdProiezione;
            } else {
                $_SESSION["mod_idproiezioneselezionata"] = "";
            }
            $_SESSION["mod_nomefilmselezionato"] = $mod_nomeNuovoFilm;
        } else {
            $_SESSION["mod_nomefilmselezionato"] = $mod_nomeFilm;
            $_SESSION["mod_idproiezioneselezionata"] = $mod_IdProiezione;
        }


    } else if(isset($_POST["method"]) && $_POST["method"] == "Elimina Proiezione") {
        $mod_IdProiezione = $_POST["mod_idProiezione"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        if($connectionOk) {
            if($connection->deleteProiezione($mod_IdProiezione)) {
                $_SESSION["success"] = true;
                $_SESSION["feedback"] = "Proiezione eliminata con successo!";
            } else {
                $_SESSION["success"] = false;
                $_SESSION["feedback"] = "Proiezione non trovata :(";
            }
            $connection->closeConnection();
        } else {
            $_SESSION["success"] = false;
            $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
        }
        $_SESSION["method"] = $_POST["method"];
        if($_SESSION["success"] == true) {
            $_SESSION["mod_idproiezioneselezionata"] = "";
        } else {
            $_SESSION["mod_idproiezioneselezionata"] = $mod_IdProiezione;
        }
        $_SESSION["mod_nomefilmselezionato"] = $_POST["mod_nomeFilm"];
    }
}

function printFilms(&$htmlPage) {
    $p_nomifilm = "<nomifilm/>";

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $template = "<option value=\"nomefilm\">nomefilm</option>";
        $stringa = "";
        $films = $connection->getNomiFilmApprovati();
        $connection->closeConnection();
        if($films) {
            foreach($films as $film) {
                $stringa .= str_replace("nomefilm", Sanitizer::forHtml($film["nome"]), $template);
            }
        }
        $htmlPage = str_replace($p_nomifilm, $stringa, $htmlPage);
    } else {
        $htmlPage = str_replace($p_nomifilm, "", $htmlPage);
    }
}

function printAggiungiProiezione(&$htmlPage) {
    if(isset($_SESSION["method"]) && $_SESSION["method"] == "Aggiungi Proiezione") {
        $htmlPage = str_replace("agg_nomefilmselezionato" , Sanitizer::forHtml($_SESSION["agg_nomefilmselezionato"]) , $htmlPage);
        $htmlPage = str_replace("agg_dataselezionata"     , Sanitizer::forHtml($_SESSION["agg_dataselezionata"])     , $htmlPage);
        $htmlPage = str_replace("aggiungiposted"          , ""                                                       , $htmlPage);

        Utils::printFeedback($htmlPage, "<feedback/>");

        unset($_SESSION["agg_nomefilmselezionato"]);
        unset($_SESSION["agg_dataselezionata"]);
        unset($_SESSION["method"]);
        unset($_SESSION["success"]);
    } else {
        $htmlPage = str_replace("aggiungiposted"          , "checked" , $htmlPage);
        $htmlPage = str_replace("agg_nomefilmselezionato" , ""        , $htmlPage);
        $htmlPage = str_replace("agg_dataselezionata"     , ""        , $htmlPage);
    }
}

function printModificaProiezione(&$htmlPage) {
    if(isset($_SESSION["method"]) && ($_SESSION["method"] == "Modifica Proiezione" || $_SESSION["method"] == "Elimina Proiezione")) {
        $htmlPage = str_replace("mod_nomefilmselezionato" , Sanitizer::forHtml($_SESSION["mod_nomefilmselezionato"]) , $htmlPage);
        $htmlPage = str_replace("modificaposted"          , "" , $htmlPage);

        if($_SESSION["method"] == "Elimina Proiezione") 
        {
            $htmlPage = str_replace("mod_idproiezioneselezionata" , "" , $htmlPage);
        } else {
            $htmlPage = str_replace("mod_idproiezioneselezionata" , Sanitizer::forHtml($_SESSION["mod_idproiezioneselezionata"]) , $htmlPage);
        }

        Utils::printFeedback($htmlPage, "<feedback/>");

        unset($_SESSION["mod_nomefilmselezionato"]);
        unset($_SESSION["mod_idproiezioneselezionata"]);
        unset($_SESSION["method"]);
        unset($_SESSION["success"]);
    } else {
        $htmlPage = str_replace("modificaposted"              , "checked" , $htmlPage);
        $htmlPage = str_replace("mod_nomefilmselezionato"     , ""        , $htmlPage);
        $htmlPage = str_replace("mod_idproiezioneselezionata" , ""        , $htmlPage);
    }
}

if(isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();
    aggiungiProiezione();
    modificaProiezione();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/adminProiezioni.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printFilms($htmlPage);
    printAggiungiProiezione($htmlPage);
    printModificaProiezione($htmlPage);
    Utils::feedbackCleanUp($htmlPage, "<feedback/>");

    echo $htmlPage;
}

?>