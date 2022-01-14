<?php

include "../php/login.php";

use DB\DBAccess;

$email = $_GET["email"];

$connection = new DBAccess();
$connectionOk = $connection->openDB();

$results = array();

if ($connectionOk) {
    $results_user = $connection->getUserByEmail($email);
    $results_tickets = $connection->getUserTicketsByEmail($email);
    $connection->closeConnection();

    if ($results_user != null) array_push($results, $results_user);
    if ($results_tickets != null) array_push($results, $results_tickets);
}

header("Content-type: application/json");
echo Sanitizer::forJson($results);