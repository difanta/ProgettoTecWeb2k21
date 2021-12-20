<?php

include_once "db.php";

use DB\DBAccess;

$paginaHtml = file_get_contents("contest.html");

$messaggi = "";
$titolo = "";
$descrizione = "";
$linkyt = "";
$email = "";

if (isset($_POST["submit"])) {
    $titolo = $_POST["titolo"];
    $descrizione = $_POST["descrizione"];
    $linkyt = $_POST["linkyt"];
    $email = $_POST["email"];

    if (strlen($titolo) == 0) {
        $messaggi .= "<li>Titolo non presente</li>";
    } elseif (preg_match('/\d/', $titolo)) {
        $messaggi .= "<li>Il nome non può contenere numeri</li>";
    }

    if (strlen($descrizione) == 0) {
        $messaggi .= "<li>Descrizione non presente</li>";
    }

    if (strlen($linkyt) == 0) {
        $messaggi .= "<li>Descrizione non presente</li>";
    }

    if (strlen($email) == 0) {
        $messaggi .= "<li>Descrizione non presente</li>";
    }

    if (strlen($messaggi) == "") {

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if($connectionOk){ // TODO
            $result = $connection->get("insert into Film(id, nome, descrizione, durata, anno, regista, produttore, cast, in_gara, approvato, candidatore) values (1, \"\");");
        }

        $messaggi .= "<p>Candidatura inviata con successo.</p>"
    }
}

?>