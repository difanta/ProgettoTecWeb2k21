<?php

session_start();

include "../php/login.php";

use DB\DBAccess;

function returnFilm() {
    if(!Login::is_logged_admin()) { return ""; }

    $mod_nomeFilm = "";

    if(isset($_POST["mod_oldNomeFilm"])) { $mod_nomeFilm = $_POST["mod_oldNomeFilm"]; }
    else { return ""; }

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $stringa = "";
        $film = $connection->getFilm($mod_nomeFilm);
        $connection->closeConnection();
        if($film && $film[0]) {
            $stringa = Sanitizer::forJson($film[0]);
        }
        header("Content-type: application/json");
        return $stringa;
    } else {
        http_response_code(500);
    }
}

function aggiungiFilm() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"]) && $_POST["method"] == "Aggiungi Film") {
        $agg_nomeFilm         = $_POST["agg_nomeFilm"]        ;
        $agg_Produttore       = $_POST["agg_Produttore"]      ;
        $agg_Regista          = $_POST["agg_Regista"]         ;
        $agg_Anno             = $_POST["agg_Anno"]            ;
        $agg_Durata           = $_POST["agg_Durata"]          ;
        $agg_descrizioneFilm  = $_POST["agg_descrizioneFilm"] ;
        $agg_Cast             = $_POST["agg_Cast"]            ;
        $agg_inGara     = false;
        $agg_Approvato  = false;
        if(isset($_POST["agg_inGara"]) && $_POST["agg_inGara"] == "on") 
        { $agg_inGara = true; }
        else 
        { $agg_inGara = false; }

        if(isset($_POST["agg_Approvato"]) && $_POST["agg_Approvato"] == "on") 
        { $agg_Approvato = true; }
        else 
        { $agg_Approvato = false; }

        if(Utils::validate($agg_nomeFilm, Utils::titoloRegex)
        && Utils::validate($agg_Produttore, Utils::regista_produttoreRegex)
        && Utils::validate($agg_Regista, Utils::regista_produttoreRegex)
        && Utils::validate($agg_Anno, Utils::annoRegex)
        && Utils::validate($agg_Durata, Utils::durataRegex)
        && Utils::validate($agg_descrizioneFilm, Utils::descrizioneRegex)
        && Utils::validate($agg_Cast, Utils::castRegex)) {

            $connection = new DBAccess();
            $connectionOk = $connection->openDB();
        
            if($connectionOk) {
                if($connection->addFilm($agg_nomeFilm, $agg_Produttore, $agg_Regista, $agg_Anno, $agg_Durata, $agg_descrizioneFilm, $agg_Cast, $agg_inGara, $agg_Approvato)) {
                    $_SESSION["success"] = true;
                    $_SESSION["feedback"] = "Film aggiunto con successo!";
                } else {
                    $_SESSION["success"] = false;
                    $_SESSION["feedback"] = "Nome film già in uso :(";
                }
                $connection->closeConnection();
            } else {
                $_SESSION["success"] = false;
                $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
            }
        } else {
            $_SESSION["success"] = false;
            $_SESSION["feedback"] = "Errore nella compilazione della form, per maggiori informazioni attivare javascript nel browser";
        }
        $_SESSION["method"]               = $_POST["method"]     ;
        $_SESSION["agg_nomefilm"]         = $agg_nomeFilm        ;
        $_SESSION["agg_produttore"]       = $agg_Produttore      ;
        $_SESSION["agg_regista"]          = $agg_Regista         ;
        $_SESSION["agg_annofilm"]         = $agg_Anno            ;
        $_SESSION["agg_duratafilm"]       = $agg_Durata          ;
        $_SESSION["agg_descrizionefilm"]  = $agg_descrizioneFilm ;
        $_SESSION["agg_cast"]             = $agg_Cast            ;
        $_SESSION["agg_filmingara"]       = $agg_inGara          ;
        $_SESSION["agg_approvato"]        = $agg_Approvato       ;
    }
}

function modificaFilm() {
    if(!Login::is_logged_admin()) { return; }

    if(isset($_POST["method"]) && $_POST["method"] == "Modifica Film") {
        $mod_oldnomefilm      = $_POST["mod_oldNomeFilm"]     ;
        $mod_nomeFilm         = $_POST["mod_nomeFilm"]        ;
        $mod_Produttore       = $_POST["mod_Produttore"]      ;
        $mod_Regista          = $_POST["mod_Registi"]         ;
        $mod_Anno             = $_POST["mod_Anno"]            ;
        $mod_Durata           = $_POST["mod_Durata"]          ;
        $mod_descrizioneFilm  = $_POST["mod_descrizioneFilm"] ;
        $mod_Cast             = $_POST["mod_Cast"]            ;
        $mod_inGara     = false;
        $mod_Approvato  = false;
        if(isset($_POST["mod_inGara"]) && $_POST["mod_inGara"] == "on") 
        { $mod_inGara = true; }
        else 
        { $mod_inGara = false; }

        if(isset($_POST["mod_Approvato"]) && $_POST["mod_Approvato"] == "on") 
        { $mod_Approvato = true; }
        else 
        { $mod_Approvato = false; }

        if(Utils::validate($mod_nomeFilm, Utils::titoloRegex)
        && Utils::validate($mod_Produttore, Utils::regista_produttoreRegex)
        && Utils::validate($mod_Regista, Utils::regista_produttoreRegex)
        && Utils::validate($mod_Anno, Utils::annoRegex)
        && Utils::validate($mod_Durata, Utils::durataRegex)
        && Utils::validate($mod_descrizioneFilm, Utils::descrizioneRegex)
        && Utils::validate($mod_Cast, Utils::castRegex)) {

            $connection = new DBAccess();
            $connectionOk = $connection->openDB();
        
            if($connectionOk) {
                if($connection->modifyFilm($mod_oldnomefilm, $mod_nomeFilm, $mod_Produttore, $mod_Regista, $mod_Anno, $mod_Durata, $mod_descrizioneFilm, $mod_Cast, $mod_inGara, $mod_Approvato)) {
                    $_SESSION["success"] = true;
                    $_SESSION["feedback"] = "Film modificato con successo!";
                } else {
                    $_SESSION["success"] = false;
                    $_SESSION["feedback"] = "Nome film già in uso :(";
                }
                $connection->closeConnection();
            } else {
                $_SESSION["success"] = false;
                $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
            }
        } else {
            $_SESSION["success"] = false;
            $_SESSION["feedback"] = "Errore nella compilazione della form, per maggiori informazioni attivare javascript nel browser";
        }
        $_SESSION["method"]               = $_POST["method"]     ;
        $_SESSION["mod_oldnomefilm"]      = $mod_oldnomefilm     ;
        $_SESSION["mod_nomefilm"]         = $mod_nomeFilm        ;
        $_SESSION["mod_produttore"]       = $mod_Produttore      ;
        $_SESSION["mod_regista"]          = $mod_Regista         ;
        $_SESSION["mod_annofilm"]         = $mod_Anno            ;
        $_SESSION["mod_duratafilm"]       = $mod_Durata          ;
        $_SESSION["mod_descrizionefilm"]  = $mod_descrizioneFilm ;
        $_SESSION["mod_cast"]             = $mod_Cast            ;
        $_SESSION["mod_filmingara"]       = $mod_inGara          ;
        $_SESSION["mod_approvato"]        = $mod_Approvato       ;
    } else if(isset($_POST["method"]) && $_POST["method"] == "Elimina Film") {
        $connection = new DBAccess();
        $connectionOk = $connection->openDB();
    
        $mod_oldnomefilm = $_POST["mod_oldNomeFilm"]; // non c'è bisogno di controlli perchè non vengono inseriti dati ed è safe passare anche dati maliziosi al server sql tramite query preparate

        if($connectionOk) {
            if($connection->deleteFilm($mod_oldnomefilm)) {
                $_SESSION["success"] = true;
                $_SESSION["feedback"] = "Film eliminato con successo!";
            } else {
                $_SESSION["success"] = false;
                $_SESSION["feedback"] = "Film non trovato :(";
            }
            $connection->closeConnection();
        } else {
            $_SESSION["success"] = false;
            $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
        }

        $_SESSION["method"]          = $_POST["method"];
        $_SESSION["mod_oldnomefilm"] = $mod_oldnomefilm;
    }
}

function printFilms(&$htmlPage) {
    $p_nomifilm = "<nomifilm/>";

    $connection = new DBAccess();
    $connectionOk = $connection->openDB();

    if($connectionOk) {
        $template = "<option value=\"nomefilm\">nomefilm</option>";
        $stringa = "";
        $films = $connection->getNomiFilm();
        $connection->closeConnection();
        if($films) {
            foreach($films as $film) {
                $stringa .= str_replace("nomefilm", Sanitizer::forHtml($film["nome"]), $template);
            }
        }
        $htmlPage = str_replace($p_nomifilm, $stringa, $htmlPage);
    } else {
        $htmlPage = str_replace($p_nomifilm, "", $htmlPage);
    }
}

function printAggiungiFilm(&$htmlPage) {
    if(isset($_SESSION["method"]) && $_SESSION["method"] == "Aggiungi Film") {   
        $htmlPage = str_replace("aggiungiposted"      , ""                                                   , $htmlPage);
        $htmlPage = str_replace("agg_nomefilm"        , Sanitizer::forHtml($_SESSION["agg_nomefilm"])        , $htmlPage);
        $htmlPage = str_replace("agg_produttore"      , Sanitizer::forHtml($_SESSION["agg_produttore"])      , $htmlPage);
        $htmlPage = str_replace("agg_regista"         , Sanitizer::forHtml($_SESSION["agg_regista"])         , $htmlPage);
        $htmlPage = str_replace("agg_annofilm"        , Sanitizer::forHtml($_SESSION["agg_annofilm"])        , $htmlPage);
        $htmlPage = str_replace("agg_duratafilm"      , Sanitizer::forHtml($_SESSION["agg_duratafilm"])      , $htmlPage);
        $htmlPage = str_replace("agg_descrizionefilm" , Sanitizer::forHtml($_SESSION["agg_descrizionefilm"]) , $htmlPage);
        $htmlPage = str_replace("agg_cast"            , Sanitizer::forHtml($_SESSION["agg_cast"])            , $htmlPage);

        if($_SESSION["agg_filmingara"]) 
        { $htmlPage = str_replace("agg_filmingara" , "checked" , $htmlPage); }
        else 
        { $htmlPage = str_replace("agg_filmingara" , ""        , $htmlPage); }

        if($_SESSION["agg_approvato"]) 
        { $htmlPage = str_replace("agg_approvato" , "checked" , $htmlPage); }
        else 
        { $htmlPage = str_replace("agg_approvato" , ""        , $htmlPage); }
       
        Utils::printFeedback($htmlPage, "<feedback/>");

        unset($_SESSION["agg_nomefilm"]);
        unset($_SESSION["agg_produttore"]);
        unset($_SESSION["agg_regista"]);
        unset($_SESSION["agg_annofilm"]);
        unset($_SESSION["agg_duratafilm"]);
        unset($_SESSION["agg_descrizionefilm"]);
        unset($_SESSION["agg_ingara"]);
        unset($_SESSION["agg_filmingara"]);
        unset($_SESSION["agg_approvato"]);

        unset($_SESSION["method"]);
        unset($_SESSION["success"]);
    } else {
        $htmlPage = str_replace("aggiungiposted"      , "checked" , $htmlPage);
        $htmlPage = str_replace("agg_nomefilm"        , ""        , $htmlPage);
        $htmlPage = str_replace("agg_produttore"      , ""        , $htmlPage);
        $htmlPage = str_replace("agg_regista"         , ""        , $htmlPage);
        $htmlPage = str_replace("agg_annofilm"        , ""        , $htmlPage);
        $htmlPage = str_replace("agg_duratafilm"      , ""        , $htmlPage);
        $htmlPage = str_replace("agg_descrizionefilm" , ""        , $htmlPage);
        $htmlPage = str_replace("agg_cast"            , ""        , $htmlPage);
        $htmlPage = str_replace("agg_filmingara"      , ""        , $htmlPage);
        $htmlPage = str_replace("agg_approvato"       , ""        , $htmlPage);
    }
}

function printModificaFilm(&$htmlPage) {
    if(isset($_SESSION["method"]) && $_SESSION["method"] == "Modifica Film") 
    {  
        if(isset($_SESSION["method"]) && $_SESSION["method"] == "Modifica Film" && $_SESSION["success"] == false)
        { $htmlPage = str_replace("mod_nomefilmselezionato" , Sanitizer::forHtml($_SESSION["mod_oldnomefilm"]) , $htmlPage); }
        else
        { $htmlPage = str_replace("mod_nomefilmselezionato" , Sanitizer::forHtml($_SESSION["mod_nomefilm"])    , $htmlPage); }

        $htmlPage = str_replace("modificaposted"          , ""                                                   , $htmlPage);
        $htmlPage = str_replace("mod_nomefilm"            , Sanitizer::forHtml($_SESSION["mod_nomefilm"])        , $htmlPage);
        $htmlPage = str_replace("mod_produttore"          , Sanitizer::forHtml($_SESSION["mod_produttore"])      , $htmlPage);
        $htmlPage = str_replace("mod_regista"             , Sanitizer::forHtml($_SESSION["mod_regista"])         , $htmlPage);
        $htmlPage = str_replace("mod_annofilm"            , Sanitizer::forHtml($_SESSION["mod_annofilm"])        , $htmlPage);
        $htmlPage = str_replace("mod_duratafilm"          , Sanitizer::forHtml($_SESSION["mod_duratafilm"])      , $htmlPage);
        $htmlPage = str_replace("mod_descrizionefilm"     , Sanitizer::forHtml($_SESSION["mod_descrizionefilm"]) , $htmlPage);
        $htmlPage = str_replace("mod_cast"                , Sanitizer::forHtml($_SESSION["mod_cast"])            , $htmlPage);

        if($_SESSION["mod_filmingara"]) 
        { $htmlPage = str_replace("mod_filmingara" , "checked" , $htmlPage); }
        else 
        { $htmlPage = str_replace("mod_filmingara" , ""        , $htmlPage); }

        if($_SESSION["mod_approvato"]) 
        { $htmlPage = str_replace("mod_approvato" , "checked" , $htmlPage); }
        else 
        { $htmlPage = str_replace("mod_approvato" , ""        , $htmlPage); }
       
        Utils::printFeedback($htmlPage, "<feedback/>");

        unset($_SESSION["mod_oldnomefilm"]);
        unset($_SESSION["mod_nomefilm"]);
        unset($_SESSION["mod_produttore"]);
        unset($_SESSION["mod_regista"]);
        unset($_SESSION["mod_annofilm"]);
        unset($_SESSION["mod_duratafilm"]);
        unset($_SESSION["mod_descrizionefilm"]);
        unset($_SESSION["mod_filmingara"]);
        unset($_SESSION["mod_approvato"]);
        unset($_SESSION["method"]);
        unset($_SESSION["success"]);
    } 
    else if(isset($_SESSION["method"]) && $_SESSION["method"] == "Elimina Film") {
        if(!$_SESSION["success"]) { 
            $htmlPage = str_replace("mod_nomefilmselezionato" , Sanitizer::forHtml($_SESSION["mod_oldnomefilm"]) , $htmlPage); 
        } else { 
            $htmlPage = str_replace("mod_nomefilmselezionato" , "" , $htmlPage); 
        }

        Utils::printFeedback($htmlPage, "<feedback/>");

        unset($_SESSION["mod_oldnomefilm"]);
        unset($_SESSION["method"]);
        unset($_SESSION["success"]);
    } else {
        $htmlPage = str_replace("modificaposted"          , "checked" , $htmlPage);
        $htmlPage = str_replace("mod_nomefilmselezionato" , ""        , $htmlPage);
        $htmlPage = str_replace("mod_nomefilm"            , ""        , $htmlPage);
        $htmlPage = str_replace("mod_produttore"          , ""        , $htmlPage);
        $htmlPage = str_replace("mod_regista"             , ""        , $htmlPage);
        $htmlPage = str_replace("mod_annofilm"            , ""        , $htmlPage);
        $htmlPage = str_replace("mod_duratafilm"          , ""        , $htmlPage);
        $htmlPage = str_replace("mod_descrizionefilm"     , ""        , $htmlPage);
        $htmlPage = str_replace("mod_cast"                , ""        , $htmlPage);
        $htmlPage = str_replace("mod_filmingara"          , ""        , $htmlPage);
        $htmlPage = str_replace("mod_approvato"           , ""        , $htmlPage);
    }
}

if(!isset($_POST["method"]) && isset($_POST["mod_oldNomeFilm"])) {
    echo returnFilm();
} else {
    if(isset($_POST["method"])) {
        // handle login/register/logout POST request
        Login::handleLogin();
        aggiungiFilm();
        modificaFilm();

        // redirect to same page (it will use GET request) https://en.wikipedia.org/wiki/Post/Redirect/Get
        http_response_code(303);
        header("Location: " . $_SERVER["REQUEST_URI"]);
    } else /* GET */ {
        $htmlPage = file_get_contents("template/adminListaFilm.html");

        // show login/register/logout results
        Login::printLogin($htmlPage);
        printFilms($htmlPage);
        printAggiungiFilm($htmlPage);
        printModificaFilm($htmlPage);

        echo $htmlPage;
    }
}

?>