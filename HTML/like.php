<?php 

include "../php/login.php";
use DB\DBAccess;

session_start();

if(Login::is_logged()) {
    $idFilm = $_POST["idfilm"];
    $newLikeState = $_POST["like"];

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        if($newLikeState == "true" && $connection->insertLike($_SESSION["login"], $idFilm)) {
            http_response_code(200);
        } 
        else if($newLikeState == "false" && $connection->removeLike($_SESSION["login"], $idFilm)){
            http_response_code(200);
        } 
        else {
            http_response_code(400);
        }
        $connection->closeConnection();
    } else {
        http_response_code(500);
    }
} else {
    http_response_code(401);
}

?>