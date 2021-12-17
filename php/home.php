<?php

session_start();

include "login.php";

$htmlPage = file_get_contents("../HTML/home.html");

Login::handle_login($htmlPage);

echo $htmlPage;

?>