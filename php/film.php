<?php

session_start();

include "login.php";
use DB\DBAccess;

function printFilm(&$htmlPage) {
    
}

if(isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    header("HTTP/1.1 303 See Other");
    header("Location: ./home.php");
} else /* GET */ {
    $htmlPage = file_get_contents("../HTML/home.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printFilm($htmlPage);

    echo $htmlPage;
}

?>