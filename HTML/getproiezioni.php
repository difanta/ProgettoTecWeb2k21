<?php 

session_start();

include "../php/login.php";
use DB\DBAccess;

if(Login::is_logged_admin()) { 
    $mod_nomeFilm = "";

    if(isset($_POST["mod_nomeFilm"])) { 
        $mod_nomeFilm = $_POST["mod_nomeFilm"]; 
    } else if(isset($_POST["agg_nomeFilm"])) {
        $mod_nomeFilm = $_POST["agg_nomeFilm"]; 
    }
    else { 
        http_response_code(400); return; 
    }

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $template = "<option value=\"idproiezione\" orario=\"datetime\">data time</option>";
        $stringa = "";
        $proiezioni = $connection->getProiezioni("tutti", $mod_nomeFilm, "");
        $connection->closeConnection();
        if($proiezioni) {
            foreach($proiezioni as $proiezione) {
                $p = str_replace("idproiezione" , Sanitizer::forHtml($proiezione["pid"])    , $template);
                $p = str_replace("datetime"     , Sanitizer::forHtml($proiezione["orario"]) , $p);
                $p = str_replace("data"         , Sanitizer::forHtml($proiezione["data"])   , $p);
                $p = str_replace("time"         , Sanitizer::forHtml($proiezione["ora"])    , $p);
                $stringa .= $p;
            }
        }
        echo $stringa;
    } else {
        http_response_code(500);
    }
} else {
    if(!Login::is_logged()) {
        http_response_code(401);
    } else {
        http_response_code(403);
    }
}




?>