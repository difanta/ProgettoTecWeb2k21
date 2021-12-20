<?php

session_start();

include "login.php";
use DB\DBAccess;

function printFilmPopolari(&$htmlPage) {
    $p_filmPopolare = "<filmPopolare/>";
    $connection = new DBAccess();
    $connectionOk = $connection->openDB();
    $at_least_one = false;

    if($connectionOk) {
        $result = $connection->get("SELECT * from Film 
                                                  join 
                                                  (SELECT Film, count(*) as Likes FROM _Like group by Film) as _likes 
                                                  on Film.id = _likes.Film 
                                                  where Film.in_gara = '1' 
                                                  order by Likes desc"
                                                  );
        
        if($result) {
            $template = file_get_contents("templateFilmPopolare.html");

            foreach($result as $indice => $film) {
                if($indice > 2 || !isset($film["nome"])) break; // only the first 3 and check if film is correctly defined
                $at_least_one = true;
                $film_html = str_replace("titolofilm", $film["nome"], $template);
                $htmlPage  = str_replace($p_filmPopolare, $film_html . $p_filmPopolare, $htmlPage);
            }
        }
    } else {
        echo "connection error";
    }

    if($at_least_one) {
        $htmlPage  = str_replace($p_filmPopolare, "", $htmlPage);
    } else {
        $htmlPage  = str_replace($p_filmPopolare, "<p>Non siamo riusciti a recuperare i dati sulle votazioni ai film!</p>", $htmlPage);
    }

}

if(isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handle_login();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header("HTTP/1.1 303 See Other");
    header("Location: ./home.php");
} else /* GET */ {
    $htmlPage = file_get_contents("../HTML/home.html");

    // show login/register/logout results
    Login::set_login_contents($htmlPage);
    printFilmPopolari($htmlPage);

    echo $htmlPage;
}


?>