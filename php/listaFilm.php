<?php

session_start();

include "login.php";
use DB\DBAccess;

function printFilms(&$htmlPage) {
    $p_filmPreview= "<filmPreview/>";
    $nomeFilm = "";
    $in_gara = "tutti";
    if(isset($_GET["nomeFilm"]))
    {
        $nomeFilm = $_GET["nomeFilm"];
        $in_gara = $_GET["gara"];
    }
    
    $connection = new DBAccess();
    $connectionOk = $connection->openDB();
    $at_least_one = false;

    if($connectionOk) {
        $result = null;
        
        $filmNameQuery = "";
        if($nomeFilm != "") {
            $filmNameQuery = "Film.name = " . $nomeFilm;
        }

        switch ($in_gara) {
            case 'tutti':
                if($nomeFilm != "") {
                    $result = $connection->get("SELECT * from Film where " . $filmNameQuery);
                } else {
                    $result = $connection->get("SELECT * from Film");
                }
                break;

            case 'gara':
                if($nomeFilm != "") {
                    $result = $connection->get("SELECT * from Film where Film.in_gara = '1' and" . $filmNameQuery);
                } else {
                    $result = $connection->get("SELECT * from Film where Film.in_gara = '1'");
                }
            break;

            case 'noGara':
                if($nomeFilm != "") {
                    $result = $connection->get("SELECT * from Film where Film.in_gara = '0' and" . $filmNameQuery);
                } else {
                    $result = $connection->get("SELECT * from Film where Film.in_gara = '0'");
                }
            break;
        }

        if($result) {
            $template = file_get_contents("templateFilmPreview.html");

            foreach($result as $indice => $film) {
                $at_least_one = true;
                $film_html = str_replace("titolofilm", $film["nome"], $template);
                $film_html = str_replace("regista", $film["regista"], $film_html);
                if($film["in_gara"]) {
                    $film_html = str_replace("ifingara", "", $film_html);
                 } else {
                    $film_html = str_replace("ifingara", "display: none", $film_html);
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
        $htmlPage  = str_replace($p_filmPreview, "<p>Non siamo riusciti a ottenere i dati sui Film!</p>", $htmlPage);
    }
}

if(isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handle_login();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header("HTTP/1.1 303 See Other");
    header("Location: ./home.php");
} else /* GET */ {
    $htmlPage = file_get_contents("../HTML/listaFilm.html");

    // show login/register/logout results
    Login::set_login_contents($htmlPage);
    printFilms($htmlPage);

    $htmlPage = str_replace("placeholder", "style", $htmlPage);

    echo $htmlPage;
}

?>