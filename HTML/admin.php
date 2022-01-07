<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function printAdminStats(&$htmlPage){
    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if ($connectionOk) {
        $n_utenti = $connection->getNoUtenti();
        $n_biglietti = $connection->getNoBiglietti();
        $n_like = $connection->getNoLike();
        $connection->closeConnection();

        $htmlPage = str_replace("pUtentiRegistrati", $n_utenti[0]["no"], $htmlPage);
        $htmlPage = str_replace("pBigliettiVenduti", $n_biglietti[0]["no"], $htmlPage);
        $htmlPage = str_replace("pLike", $n_like[0]["no"], $htmlPage);
    }
}

if(isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/admin.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printAdminStats($htmlPage);

    echo $htmlPage;
}

?>