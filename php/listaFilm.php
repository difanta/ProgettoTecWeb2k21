<?php

session_start();

include "login.php";
use DB\DBAccess;

function printFilms(&$htmlPage) {
    $p_filmPreview = "<filmPreview/>";
    $p_nomeFilmFilter = "inputnomefilm";
    $p_tuttiFilter = "tuttiselected";
    $p_garaFilter = "garaselected";
    $p_nogaraFilter = "nogaraselected";
    
    $nomeFilm = "";
    $in_gara = "tutti";

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

        array_push($filters, "Film.approvato = '1'");
        
        // add in gara filters
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

        // add nome film filters
        if($nomeFilm != "") {
            array_push($filters, "Film.nome like '%" . $nomeFilm . "%'");
        }

        // add filters to query
        $query = "SELECT * from Film";
        foreach($filters as $index => $filter) {
            if($index == 0) {
                $query = "$query where $filter";
            } else {
                $query = "$query and $filter";
            }
        }
        unset($filters);
        $result = $connection->get($query);

        if($result) {
            $template = file_get_contents("templateFilmPreview.html");

            // create and substitute films based on template
            foreach($result as $indice => $film) {
                $at_least_one = true;
                $film_html = str_replace("titolofilm", $film["nome"]   , $template);
                $film_html = str_replace("regista"   , $film["regista"], $film_html);
                if($film["in_gara"]) {
                    Login::showElement("<ifingara>"  , "</ifingara>"   , $film_html);
                } else {
                    Login::hideElement("<ifingara>"  , "</ifingara>"   , $film_html);
                }
                $htmlPage  = str_replace($p_filmPreview, $film_html . $p_filmPreview, $htmlPage);
            }
        }
    } else {
        echo "connection error";
    }

    if($at_least_one) {
        $htmlPage  = str_replace($p_filmPreview, "", $htmlPage);
    } else {
        $htmlPage  = str_replace($p_filmPreview, "<p>Nessun Film trovato!</p>", $htmlPage);
    }
}

if(isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header("HTTP/1.1 303 See Other");
    header("Location: ./listaFilm.php");
} else /* GET */ {
    $htmlPage = file_get_contents("../HTML/listaFilm.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printFilms($htmlPage);

    echo $htmlPage;
}

?>