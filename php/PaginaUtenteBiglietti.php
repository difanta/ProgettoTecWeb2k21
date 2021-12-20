<?php

include_once "db.php";

use DB\DBAccess;

$htmlPage = file_get_contents("../HTML/PaginaUtente.html");

$connection = new DBAccess();
$connectionOk = $connection->openDB();

$biglietti = "";
$listaBiglietti = "";

if ($connectionOk) {
    // SELECT * FROM Biglietto join Utente join Film WHERE Utente.email = '$email'
    $biglietti = $connection->get("SELECT nome, Biglietto.id, orario FROM Biglietto join Film join Proiezione", true);
    $connection->closeConnection();
    if ($biglietti != null) {
        $listaBiglietti .= "<ul>";
        foreach ($biglietti as $biglietto) {
            $listaBiglietti .=
                "<li class=\"ticket\"><p><span class=\"ticket-up\">" . $biglietto["nome"] .
                "</span><span class=\"ticket-up\">#" . $biglietto["id"] .
                "</span></p><p><span class=\"ticket-low\">" . $biglietto["orario"] .
                "</span></p></li>";
        }
        unset($biglietto);
        $listaBiglietti .= "</ul>";
    } else {
        $listaBiglietti .= "<p>Errore load biglietti</p>";
    }
} else {
    $listaBiglietti .= "<p>Errore connessione db</p>";
}

echo str_replace("<listaBiglietti/>", $listaBiglietti, $htmlPage);

?>
