<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function printFilmPopolari(&$htmlPage) {
    $p_filmPopolare = "<filmPopolare/>";
    $connection = new DBAccess();
    $connectionOk = $connection->openDB();
    $at_least_one = false;

    if($connectionOk) {
        $result = $connection->getFilmPopolari();
        
        if($result) {
            $template = file_get_contents("template/templateFilmPopolare.html");

            foreach($result as $indice => $film) {
                if($indice > 2) break; // only the first 3 films
                $at_least_one = true;
                $film_html = str_replace("titolofilm"       , $film["nome"]     , $template);
                //$film_html = str_replace("percorsoimmagine" , $film["immagine"] , $template);
                $htmlPage  = str_replace($p_filmPopolare, $film_html . $p_filmPopolare, $htmlPage);
            }
        }
    }

    if($at_least_one) {
        $htmlPage  = str_replace($p_filmPopolare, "", $htmlPage);
    } else {
        $htmlPage  = str_replace($p_filmPopolare, "<p>Non siamo riusciti a recuperare i dati sulle votazioni ai film!</p>", $htmlPage);
    }

}

if(isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/home.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printFilmPopolari($htmlPage);

    echo $htmlPage;
}


?>