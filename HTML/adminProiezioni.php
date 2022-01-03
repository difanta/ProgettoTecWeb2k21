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
        } else {
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        }
        return $stringa;
    } else {
        header($_SERVER["SERVER_PROTOCOL"]." 500 Server Error");
    }
}

function returnProiezione() {
    if(!Login::is_logged_admin()) { return ""; }

    $mod_idProiezione = "";

    if(isset($_POST["mod_idProiezione"])) { $mod_idProiezione = $_POST["mod_idProiezione"]; }
    else { return ""; }

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $proiezioni = $connection->getProiezione($mod_idProiezione);
        if($proiezioni && $proiezioni[0]) {
            return "orario=".urlencode($proiezioni[0]["orario"])."&nomeFilm=".urlencode($proiezioni[0]["nome"]);
        } else {
            return "";
        }
    } else {
        return "";
    }
}

function printFilms(&$htmlPage) {
    $p_nomifilm = "<nomifilm/>";
    $p_proiezioni = "<proiezioni/>";

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();
    $at_least_one = false;

    if($connectionOk) {
        // set datalist
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

if(!isset($_POST["method"]) && isset($_POST["mod_nomeFilm"])) {
    echo returnProiezioni();
} else if(!isset($_POST["method"]) && isset($_POST["mod_idProiezione"])) {
    echo returnProiezione();
} else {
    if(isset($_POST["method"])) {
        // handle login/register/logout POST request
        Login::handleLogin();
    
        // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
        header($_SERVER["SERVER_PROTOCOL"]." 303 See Other");
        header("Location: " . $_SERVER["REQUEST_URI"]);
    } else /* GET */ {
        $htmlPage = file_get_contents("template/adminProiezioni.html");
    
        // show login/register/logout results
        Login::printLogin($htmlPage);
        printFilms($htmlPage);
    
        echo $htmlPage;
    }
}

?>