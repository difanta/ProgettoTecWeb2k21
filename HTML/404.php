<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

if(isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/404.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);

    echo $htmlPage;
}


?>