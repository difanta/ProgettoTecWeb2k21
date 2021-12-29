<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function printProiezioni(&$htmlPage) {
    $p_nomifilm = "<nomifilm/>"
    $p_proiezioni = "<proiezioni/>"
    $add_nomeFilm = "";
    $mod_nomeFilm = "";

    if(isset($_GET["add_nomeFilm"])) $add_nomeFilm = $_GET["add_nomeFilm"];
    if(isset($_GET["mod_nomeFilm"])) $mod_nomeFilm = $_GET["mod_nomeFilm"];

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();
    $at_least_one = false;

    if($connectionOk) {
        $template = "<option value=\"idproiezione\">";
        $stringa = "";
        $proiezioni = $connection->getProiezioni("tutti", $mod_nomeFilm, "");
        foreach($proiezioni as $proiezione) {
            $stringa .= str_replace("idproiezione", $proiezione["id"], $template);
        }
        $htmlPage = str_replace($p_nomifilm, $stringa, $htmlPage);
        
        // set datalist
        $template = "<option value=\"nomefilm\">";
        $stringa = "";
        $films = $connection->getNomiFilm();
        foreach($films as $film) {
            $stringa .= str_replace("nomefilm", $film["nome"], $template);
        }
        $htmlPage = str_replace($p_nomifilm, $stringa, $htmlPage);
    } else {
        $htmlPage = str_replace($p_nomifilm, "", $htmlPage);
        $htmlPage = str_replace($p_proiezioni, "", $htmlPage);
    }
}

if(isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header("HTTP/1.1 303 See Other");
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/adminProiezioni.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printProiezioni($htmlPage);

    echo $htmlPage;
}

?>