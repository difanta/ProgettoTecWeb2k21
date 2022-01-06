<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function printCandidature(&$htmlPage)
{
    $list = "";

    $c_sospesa_sel = "sospesaSel";
    $c_approvata_sel = "approvataSel";

    $filter_candidatura = "Sospesa";

    if (isset($_GET["filterCandidatura"])) {
        $filter_candidatura = $_GET["filterCandidatura"];
    }
    switch ($filter_candidatura) {
        case 'Sospesa':
            $htmlPage = str_replace($c_sospesa_sel, "selected", $htmlPage);
            $htmlPage = str_replace($c_approvata_sel, "", $htmlPage);
            break;
        case 'Approvata':
            $htmlPage = str_replace($c_sospesa_sel, "", $htmlPage);
            $htmlPage = str_replace($c_approvata_sel, "selected", $htmlPage);
            break;
    }

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if ($connectionOk) {
        $candidature = $connection->getCandidatureAndEmail($filter_candidatura);
        $connection->closeConnection();
        if ($candidature != null) {
            $list .= "<ul id='acnSospese'>";
            foreach ($candidature as $index => $candidatura) {
                $list .= file_get_contents("template/templateCandidatura.html");

                $list = str_replace("collapse", "collapse" . $index, $list);
                $list = str_replace("pTitolo", $candidatura["nome"], $list);
                $list = str_replace("pDurata", $candidatura["durata"] . "'", $list);
                $list = str_replace("pAnno", $candidatura["anno"], $list);
                $list = str_replace("pRegista", $candidatura["regista"], $list);
                $list = str_replace("pProduttore", $candidatura["produttore"], $list);
                $list = str_replace("pCast", $candidatura["cast"], $list);
                $list = str_replace("pEmail", $candidatura["email"], $list);
                $list = str_replace("pDescrizione", $candidatura["descrizione"], $list);

                if ($filter_candidatura == "Approvata") {
                    $list = str_replace("rifiutaCand", "disabled", $list);
                }
            }
            unset($candidatura);
            $list .= "</ul>";
        } else {
            $list .= "<p>Non sono presenti biglietti</p>";
        }
    } else {
        $list .= "<p>Errore connessione db</p>";
    }


    $htmlPage = str_replace("<listaCandidature/>", $list, $htmlPage);
}

function rifiutaCandidatura()
{
    if ($_POST["method"] == "Rifiuta candidatura") {
        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            $connection->deleteCandidatura($_POST["titolo"]);
        }
    }
}

function approvaCandidatura()
{
    if ($_POST["method"] == "Accetta candidatura") {
        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if ($connectionOk) {
            $connection->approvaCandidatura($_POST["titolo"]);
        }
    }
}

if (isset($_POST["method"])) {
    // handle login/register/logout POST request
    Login::handleLogin();
    rifiutaCandidatura();
    approvaCandidatura();

    // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
    http_response_code(303);
    header("Location: " . $_SERVER["REQUEST_URI"]);
} else /* GET */ {
    $htmlPage = file_get_contents("template/adminCandidature.html");

    // show login/register/logout results
    Login::printLogin($htmlPage);
    printCandidature($htmlPage);

    echo $htmlPage;
}