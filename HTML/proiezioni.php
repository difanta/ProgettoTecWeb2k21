<?php

session_start();

include "../php/login.php";
use DB\DBAccess;

function printProiezioni(&$htmlPage) {
    $p_nomifilm = "<nomifilm/>";
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
    $htmlPage = str_replace($p_dataProiezioneFilter, Sanitizer::forHtml($dataProiezione), $htmlPage);

    if(isset($_GET["nomeFilm"])) { 
        $nomeFilm = $_GET["nomeFilm"]; 
    }
    $htmlPage = str_replace($p_nomeFilmFilter, Sanitizer::forHtml($nomeFilm), $htmlPage);

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
        $result = $connection->getProiezioni($in_gara, $nomeFilm, $dataProiezione);
        $films = $connection->getNomiFilmApprovati();
        $connection->closeConnection();

        if($result) {
            $template = file_get_contents("template/templateProiezione.html");

            // create and substitute proiezione based on template
            foreach($result as $indice => $film) {
                $at_least_one = true;
                $proiezione_html = str_replace("idfilm",         Sanitizer::forHtml($film["fid"]),     $template);
                $proiezione_html = str_replace("titolofilmlink", Sanitizer::forLinks($film["nome"]),   $proiezione_html);
                $proiezione_html = str_replace("titolofilm",     Sanitizer::forHtml($film["nome"]),    $proiezione_html);
                $proiezione_html = str_replace("regista",        Sanitizer::forHtml($film["regista"]), $proiezione_html);
                $proiezione_html = str_replace("data",           Sanitizer::forHtml(Utils::strftimeIta("%e %B", strtotime($film["data"]))),    $proiezione_html);
                $proiezione_html = str_replace("ora",            Sanitizer::forHtml($film["ora"]),     $proiezione_html);
                $proiezione_html = str_replace("idproiezione",   Sanitizer::forHtml($film["pid"]),     $proiezione_html);
                if($film["in_gara"]){
                    $proiezione_html = str_replace("filmNonInGara" , "filmInGara" , $proiezione_html);
                }

                $htmlPage  = str_replace($p_proiezione, $proiezione_html . $p_proiezione, $htmlPage);
            }
        }
        
        // set datalist
        $template = "<option value=\"nomefilm\"></option>";
        $stringa = "";
        if($films) {
            foreach($films as $film) {
                $stringa .= str_replace("nomefilm", Sanitizer::forHtml($film["nome"]), $template);
            }
        }
        $htmlPage = str_replace($p_nomifilm, $stringa, $htmlPage);
    } else {
        $htmlPage = str_replace($p_nomifilm, "", $htmlPage);
    }

    if($at_least_one) {
        $htmlPage  = str_replace($p_proiezione, "", $htmlPage);
    } else {
        $htmlPage  = str_replace($p_proiezione, "<p>Nessuna Proiezione trovata!</p>", $htmlPage);
    }
}

if(isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/proiezioni.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printProiezioni($htmlPage);

    echo $htmlPage;
}