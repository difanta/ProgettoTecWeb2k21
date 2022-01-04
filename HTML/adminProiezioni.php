<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function returnProiezioni() {
    if(!Login::is_logged_admin()) { return ""; }

    $mod_nomeFilm = "";

    if(isset($_POST["mod_nomeFilm"])) { $mod_nomeFilm = $_POST["mod_nomeFilm"]; }
    else { return ""; }

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $template = "<option value=\"idproiezione\" orario=\"datetime\">data time</option>";
        $stringa = "";
        $proiezioni = $connection->getProiezioni("tutti", $mod_nomeFilm, "");
        if($proiezioni) {
            foreach($proiezioni as $proiezione) {
                $p = str_replace("idproiezione" , $proiezione["pid"]    , $template);
                $p = str_replace("datetime"     , $proiezione["orario"] , $p);
                $p = str_replace("data"         , $proiezione["data"]   , $p);
                $p = str_replace("time"         , $proiezione["ora"]    , $p);
                $stringa .= $p;
            }
        }
        return $stringa;
    } else {
        http_response_code(500);
    }
}

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
            } else {
                $_SESSION["success"] = false; 
            }
        } else {
            $_SESSION["success"] = false;
        }
        $_SESSION["method"] = $_POST["method"];
        $_SESSION["agg_nomefilmselezionato"] = $agg_nomeFilm;
        $_SESSION["agg_dataselezionata"] = $agg_data;
    }
}

function modificaProiezione() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"]) && $_POST["method"] == "Modifica Proiezione") {
        $mod_IdProiezione = $_POST["mod_idProiezione"]; 
        $mod_nomeNuovoFilm = $_POST["mod_nomeNuovoFilm"];
        $mod_nuovaData = $_POST["mod_nuovaData"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        if($connectionOk) {
            if($connection->modifyProiezione($mod_IdProiezione, $mod_nomeNuovoFilm, $mod_nuovaData)) {
                $_SESSION["success"] = true;
            } else {
                $_SESSION["success"] = false; 
            }
        } else {
            $_SESSION["success"] = false;
        }
        $_SESSION["method"] = $_POST["method"];
        $_SESSION["mod_idproiezioneselezionata"] = $mod_IdProiezione;
        $_SESSION["mod_nomefilmselezionato"] = $_POST["mod_nomeFilm"];

    } else if(isset($_POST["method"]) && $_POST["method"] == "Elimina Proiezione") {
        $mod_IdProiezione = $_POST["mod_idProiezione"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        if($connectionOk) {
            if($connection->deleteProiezione($mod_IdProiezione)) {
                $_SESSION["success"] = true;
            } else {
                $_SESSION["success"] = false; 
            }
        } else {
            $_SESSION["success"] = false;
        }
        $_SESSION["method"] = $_POST["method"];
        $_SESSION["mod_idproiezioneselezionata"] = $mod_IdProiezione;
        $_SESSION["mod_nomefilmselezionato"] = $_POST["mod_nomeFilm"];
    }
}

function printFilms(&$htmlPage) {
    $p_nomifilm = "<nomifilm/>";
    $p_proiezioni = "<proiezioni/>";

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $template = "<option value=\"nomefilm\">nomefilm</option>";
        $stringa = "";
        $films = $connection->getNomiFilm();
        foreach($films as $film) {
            $stringa .= str_replace("nomefilm", $film["nome"], $template);
        }
        $htmlPage = str_replace($p_nomifilm, $stringa, $htmlPage);
    } else {
        $htmlPage = str_replace($p_proiezioni, "", $htmlPage);
        $htmlPage = str_replace($p_nomifilm, "", $htmlPage);
    }
}

function printAggiungiProiezione(&$htmlPage) {
    if(isset($_SESSION["method"]) && $_SESSION["method"] == "Aggiungi Proiezione") {
        $htmlPage = str_replace("agg_nomefilmselezionato" , $_SESSION["agg_nomefilmselezionato"] , $htmlPage);
        $htmlPage = str_replace("agg_dataselezionata"     , $_SESSION["agg_dataselezionata"]     , $htmlPage);

        $htmlPage = " agg successo: " . $_SESSION["success"] . $htmlPage;

        unset($_SESSION["agg_nomefilmselezionato"]);
        unset($_SESSION["agg_dataselezionata"]);
        unset($_SESSION["method"]);
        unset($_SESSION["success"]);
    } else {
        $htmlPage = str_replace("agg_nomefilmselezionato" , "" , $htmlPage);
        $htmlPage = str_replace("agg_dataselezionata"     , "" , $htmlPage);
    }
}

function printModificaProiezione(&$htmlPage) {
    if(isset($_SESSION["method"]) && ($_SESSION["method"] == "Modifica Proiezione" || $_SESSION["method"] == "Elimina Proiezione")) {
        $htmlPage = str_replace("mod_nomefilmselezionato" , $_SESSION["mod_nomefilmselezionato"] , $htmlPage);
        
        if($_SESSION["method"] == "Elimina Proiezione") {
            $htmlPage = str_replace("mod_idproiezioneselezionata" , "" , $htmlPage);
        } else {
            $htmlPage = str_replace("mod_idproiezioneselezionata" , $_SESSION["mod_idproiezioneselezionata"] , $htmlPage);
        }

        $htmlPage = "mod successo: " . $_SESSION["success"] . $htmlPage;

        unset($_SESSION["mod_nomefilmselezionato"]);
        unset($_SESSION["mod_idproiezioneselezionata"]);
        unset($_SESSION["method"]);
        unset($_SESSION["success"]);
    } else {
        $htmlPage = str_replace("mod_nomefilmselezionato"     , "" , $htmlPage);
        $htmlPage = str_replace("mod_idproiezioneselezionata" , "" , $htmlPage);
    }
}

if(!isset($_POST["method"]) && isset($_POST["mod_nomeFilm"])) {
    echo returnProiezioni();
} else {
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
    
        echo $htmlPage;
    }
}

?>