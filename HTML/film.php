<?php

session_start();

include "../php/login.php";
include "../php/fs.php";
use DB\DBAccess;

function printFilm(&$htmlPage) {
    $found = false;

    if(isset($_GET["nomeFilm"])) {
        $nomeFilm = $_GET["nomeFilm"];
    
        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        if($connectionOk) {
            $result = $connection->getFilm($nomeFilm);
            $connection->closeConnection();
            if($result) {    
                $film = $result[0];
                if($film) {
                    $found = true;
                    $htmlPage = str_replace("titolofilm"          , Sanitizer::forHtml($film["nome"])        , $htmlPage);
                    $htmlPage = str_replace("regista"             , Sanitizer::forHtml($film["regista"])     , $htmlPage);
                    $htmlPage = str_replace("elencoattori"        , Sanitizer::forHtml($film["cast"])        , $htmlPage);
                    $htmlPage = str_replace("annodipubblicazione" , Sanitizer::forHtml($film["anno"])        , $htmlPage);
                    $htmlPage = str_replace("nomeproduttore"      , Sanitizer::forHtml($film["produttore"])  , $htmlPage);
                    $htmlPage = str_replace("duratafilm"          , Sanitizer::forHtml($film["durata"])      , $htmlPage);
                    $htmlPage = str_replace("descrizioneimmagine" , Sanitizer::forHtml($film["alt"])         , $htmlPage);
                    $htmlPage = str_replace("descrizionefilm"     , Sanitizer::forHtml($film["descrizione"]) , $htmlPage);
                    $htmlPage = str_replace("percorsoimmagine"    , FS::findImage($film["nome"]), $htmlPage);
                    if($film["in_gara"]){
                        $htmlPage = str_replace("filmingara" , "sì" , $htmlPage);
                    }
                    else{
                        $htmlPage = str_replace("filmingara" , "no" , $htmlPage);
                    }
                }
            }
        }
    }

    if($found) {
        Login::showElement("<iffilmfound>"    , "</iffilmfound>"    , $htmlPage);
        Login::hideElement("<iffilmnotfound>" , "</iffilmnotfound>" , $htmlPage);
    } else {
        Login::hideElement("<iffilmfound>"    , "</iffilmfound>"    , $htmlPage);
        Login::showElement("<iffilmnotfound>" , "</iffilmnotfound>" , $htmlPage);
    }
}

if(isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/templateFilm.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printFilm($htmlPage);

    echo $htmlPage;
}

?>