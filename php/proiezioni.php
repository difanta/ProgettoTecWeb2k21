<?php

session_start();

include "login.php";
use DB\DBAccess;

function printProiezioni(&$htmlPage) {
    $p_proiezione= "<proiezione/>";
    $p_nomeFilmFilter = "inputnomefilm";
    $p_dataProiezioneFilter = "inputdata";
    $p_tuttiFilter = "tuttiselected";
    $p_garaFilter = "garaselected";
    $p_nogaraFilter = "nogaraselected";

    $nomeFilm = "";
    $dataProiezione = "";
    $in_gara = "tutti";
    
    if(isset($_GET["data"])) { 
        $dataProiezione = $_GET["data"]; 
    }
    $htmlPage = str_replace($p_dataProiezioneFilter, $dataProiezione, $htmlPage);

    if(isset($_GET["nomeFilm"])) { 
        $nomeFilm = $_GET["nomeFilm"]; 
    }
    $htmlPage = str_replace($p_nomeFilmFilter, $nomeFilm, $htmlPage);

    if(isset($_GET["gara"])) { 
        $in_gara = $_GET["gara"]; 
    }
    switch ($in_gara) {
        case 'tutti':
            $htmlPage = str_replace($p_tuttiFilter, "selected", $htmlPage);
            $htmlPage = str_replace($p_nogaraFilter, "", $htmlPage);
            $htmlPage = str_replace($p_garaFilter, "", $htmlPage);
            break;
        case 'gara':
            $htmlPage = str_replace($p_tuttiFilter, "", $htmlPage);
            $htmlPage = str_replace($p_nogaraFilter, "", $htmlPage);
            $htmlPage = str_replace($p_garaFilter, "selected", $htmlPage);
            break;
        case 'noGara':
            $htmlPage = str_replace($p_tuttiFilter, "", $htmlPage);
            $htmlPage = str_replace($p_nogaraFilter, "selected", $htmlPage);
            $htmlPage = str_replace($p_garaFilter, "", $htmlPage);
            break;
    }

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();
    $at_least_one = false;

    if($connectionOk) {
        $filters = array();
        
        switch ($in_gara) {
            case 'tutti':
                break;
            case 'gara':
                array_push($filters, "Film.in_gara = '1'");
            break;
            case 'noGara':
                array_push($filters, "Film.in_gara = '0'");
            break;
        }

        if($nomeFilm != "") {
            array_push($filters, "Film.nome like '%" . $nomeFilm . "%'");
        }

        $query = "SELECT * from Film";
        foreach($filters as $index => $filter) {
            if($index == 0) {
                $query = "$query where $filter";
            } else {
                $query = "$query and $filter";
            }
        }

        if($dataProiezione != "") {
            $query = "SELECT *, Proiezione.id as pid, CAST(orario AS DATE) as data, TIME_FORMAT(CAST(orario AS TIME), '%H:%i') as ora from Proiezione join ($query) as Film on Proiezione.film = Film.id where CAST(orario AS DATE) = '$dataProiezione' order by orario asc";
        } else {
            $query = "SELECT *, Proiezione.id as pid, CAST(orario AS DATE) as data, TIME_FORMAT(CAST(orario AS TIME), '%H:%i') as ora from Proiezione join ($query) as Film on Proiezione.film = Film.id order by orario asc";
        }

        unset($filters);
        $result = $connection->get($query);

        if($result) {
            $template = file_get_contents("templateProiezione.html");

            foreach($result as $indice => $film) {
                $at_least_one = true;
                $proiezione_html = str_replace("titolofilm", $film["nome"], $template);
                $proiezione_html = str_replace("regista", $film["regista"], $proiezione_html);
                $proiezione_html = str_replace("data", $film["data"], $proiezione_html);
                $proiezione_html = str_replace("ora", $film["ora"], $proiezione_html);
                $proiezione_html = str_replace("id", $film["pid"], $proiezione_html);
                $htmlPage  = str_replace($p_proiezione, $proiezione_html . $p_proiezione, $htmlPage);
            }
        }
    } else {
        echo "connection error";
    }

    if($at_least_one) {
        $htmlPage  = str_replace($p_proiezione, "", $htmlPage);
    } else {
        $htmlPage  = str_replace($p_proiezione, "<p>Nessuna Proiezione trovata!</p>", $htmlPage);
    }
}

if(isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handle_login();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header("HTTP/1.1 303 See Other");
    header("Location: ./proiezioni.php");
} else /* GET */ {
    $htmlPage = file_get_contents("../HTML/proiezioni.html");

    // show login/register/logout results
    Login::set_login_contents($htmlPage);
    printProiezioni($htmlPage);

    echo $htmlPage;
}