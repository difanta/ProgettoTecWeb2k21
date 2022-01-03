<?php

session_start();

include "../php/login.php";
use DB\DBAccess;

function printFilm(&$htmlPage) {
    $found = false;

    if(isset($_GET["nomeFilm"])) {
        $nomeFilm = $_GET["nomeFilm"];
    
        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        if($connectionOk) {
            $result = $connection->getFilm($nomeFilm);
    
            if($result) {    
                $film = $result[0];
                if($film) {
                    $found = true;
                    $htmlPage = str_replace("titolofilm"          , $film["nome"]        , $htmlPage);
                    $htmlPage = str_replace("regista"             , $film["regista"]     , $htmlPage);
                    $htmlPage = str_replace("annodipubblicazione" , $film["anno"]        , $htmlPage);
                    $htmlPage = str_replace("nomeproduttore"      , $film["produttore"]  , $htmlPage);
                    $htmlPage = str_replace("duratafilm"          , $film["durata"]      , $htmlPage);
                    $htmlPage = str_replace("descrizionefilm"     , $film["descrizione"] , $htmlPage);
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
    header($_SERVER["SERVER_PROTOCOL"]." 303 See Other");
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/templateFilm.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printFilm($htmlPage);

    echo $htmlPage;
}

?>