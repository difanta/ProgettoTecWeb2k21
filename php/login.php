<?php

include_once "db.php";
include_once "sanitizer.php";
include_once "Utils.php";

use DB\DBAccess;

/* Input control feedback messages

$p_login_email_message = "";
$p_login_password_message = "";
$p_login_general_message = "";

$p_register_name_message = "";
$p_register_surname_message = "";
$p_register_date_of_birth_message = "";
$p_register_email_message = "";
$p_register_password_message = "";
$p_login_general_message = "";

// messages
$login_email_message = "";
$login_password_message = "";
$login_general_message = "";

$register_name_message = "";
$register_surname_message = "";
$register_date_of_birth_message = "";
$register_email_message = "";
$register_password_message = "";
$login_general_message = "";

*/

class Login {
    private static $p_account_dropdown = "<div id=\"accountDropdown\"";
    private static $p_account_section = "<div id=\"accountSection\" class=\"slidableOut";
    private static $p_login_section = "<div id=\"loginSection\" class=\"slidableIn";
    private static $p_signup_section = "<div id=\"signupSection\" class=\"slidableIn";

    private static $p_if_logged_open = "<iflogged>";                    // show if logged
    private static $p_if_logged_close = "</iflogged>";
    
    private static $p_if_not_logged_open = "<ifnotlogged>";             // show if not logged
    private static $p_if_not_logged_close = "</ifnotlogged>";

    private static $p_if_logged_admin_open = "<ifloggedadmin>";
    private static $p_if_logged_admin_close = "</ifloggedadmin>";

    private static $p_if_not_logged_admin_open = "<ifnotloggedadmin>";
    private static $p_if_not_logged_admin_close = "</ifnotloggedadmin>";

    private static $p_accountname = "<accountname/>";

    public static function showElement($open, $close, &$string) {
        $string = str_replace($open, "", $string);
        if($open != $close) 
        { $string = str_replace($close, "", $string); }
    }
    
    public static function hideElement($open, $close, &$string) {
        $string = preg_replace("#".$open."[\s\S]*?".$close."#", "", $string);
    }

    public static function is_logged() {
        return isset($_SESSION["login"]);
    }

    public static function is_logged_admin() {
        return (Login::is_logged() && $_SESSION["is_admin"] == true);
    }

    public static function handleLogin() {
        if(!Login::is_logged()) // if not logged try to login or register
        {
            if($_POST["method"] == "login") 
            {
                $email    = strtolower($_POST["email"]);
                $password = $_POST["password"];

                $_SESSION["success"] = false;

                if(Utils::validate($email, Utils::emailRegexLogin)
                && Utils::validate($password, Utils::passwordRegexLogin)) {

                    $connection   = new DBAccess();
                    $connectionOk = $connection->openDB();

                    if($connectionOk) {
                        $result = $connection->getUserByEmail($email);
                        if($result && $result[0] && ($result[0]["password"] == $password)) 
                        {
                            $_SESSION["success"]     = true;
                            $_SESSION["login"]       = $result[0]["id"]; 
                            $_SESSION["accountname"] = ucfirst($result[0]["nome"]); 
                            $_SESSION["is_admin"]    = $result[0]["admin"]; 
                            $_SESSION["feedback"] = "Login effettuato con successo!";
                        } else {
                            $_SESSION["feedback"] = "Email o password sbagliata :( \n Ritenta";
                        }
                        $connection->closeConnection();
                    } else {
                        $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
                    }
                } else {
                    $_SESSION["feedback"] = "Errore nella compilazione della form, per maggiori informazioni attivare javascript nel browser";
                }

                if($_SESSION["success"] == false) 
                {
                    $_SESSION["email"] = $email;
                }
            } 
            elseif ($_POST["method"] == "register") 
            {
                $nome            = $_POST["nome"];
                $cognome         = $_POST["cognome"];
                $data_di_nascita = $_POST["data_di_nascita"];
                $email           = strtolower($_POST["email"]);
                $password        = $_POST["password"];

                $_SESSION["success"] = false;

                if(Utils::validate($nome, Utils::namesRegex)
                && Utils::validate($cognome, Utils::namesRegex)
                && Utils::validate($email, Utils::emailRegex)
                && Utils::validate($password, Utils::passwordRegex)) {
                    $connection = new DBAccess();
                    $connectionOk = $connection->openDB();

                    if($connectionOk) 
                    {
                        if($connection->insertUser($nome, $cognome, $data_di_nascita, $email, $password)) { 
                            $_SESSION["success"] = true; 
                            $_SESSION["feedback"] = "Registrato con successo! Ora effettua il login";
                        } else {
                            $_SESSION["feedback"] = "Email già in uso :(";
                        }
                        $connection->closeConnection();
                    } else {
                        $_SESSION["feedback"] = "Errore nei nostri server, ci scusiamo :(";
                    }
                } else {
                    $_SESSION["feedback"] = "Errore nella compilazione della form, per maggiori informazioni attivare javascript nel browser";
                }

                if($_SESSION["success"] == false) 
                {
                    $_SESSION["nome"]            = $nome;
                    $_SESSION["cognome"]         = $cognome;
                    $_SESSION["data_di_nascita"] = $data_di_nascita;
                    $_SESSION["email"]           = $email;
                }
            }
        } 
        else /* Not logged: you can only logout */
        { 
            if($_POST["method"] == "logout") {
                session_unset();
                session_destroy();
                session_start();
                $_SESSION["success"] = true;
                $_SESSION["feedback"] = "Logout effettuato con successo!";
            }
        }
        if(isset($_SESSION["success"])) {
            $_SESSION["method"] = $_POST["method"];
        }
    }

    public static function printLogin(&$htmlPage) {
        if(isset($_SESSION["method"]) && ($_SESSION["method"] == "login" || $_SESSION["method"] == "logout" || $_SESSION["method"] == "register"))
        {
            $method = $_SESSION["method"];
            $success = $_SESSION["success"];

            switch ($method) 
            {
                case 'login':
                    $htmlPage = str_replace(Login::$p_account_dropdown, Login::$p_account_dropdown . " class=\"dropdown\"", $htmlPage);
                    
                    if($success) {
                        Utils::printFeedback($htmlPage, "<loginfeedback/>");
                    } else {
                        $htmlPage = str_replace(Login::$p_account_section,  Login::$p_account_section  . " slideOut\"",         $htmlPage);
                        $htmlPage = str_replace(Login::$p_login_section,    Login::$p_login_section    . " slideIn\"",          $htmlPage);
                        Utils::printFeedback($htmlPage, "<loginfeedback/>");
                    }

                    if(!$success) {
                        unset($_SESSION["email"]);
                    }

                    break;

                case 'register':
                    $htmlPage = str_replace(Login::$p_account_dropdown, Login::$p_account_dropdown . " class=\"dropdown\"", $htmlPage);
                    
                    if($success) {
                        Utils::printFeedback($htmlPage, "<registerfeedbackpositive/>");
                    } else {
                        $htmlPage = str_replace(Login::$p_account_section,  Login::$p_account_section  . " slideOut\"",         $htmlPage);
                        $htmlPage = str_replace(Login::$p_signup_section,   Login::$p_signup_section   . " slideIn\"",          $htmlPage);
                        Utils::printFeedback($htmlPage, "<registerfeedbacknegative/>");
                    }

                    if(!$success) {
                        unset($_SESSION["nome"]);
                        unset($_SESSION["cognome"]);
                        unset($_SESSION["data_di_nascita"]);
                        unset($_SESSION["email"]);
                    }

                    break;

                case 'logout':
                    Utils::printFeedback($htmlPage, "<logoutfeedback/>");
                    $htmlPage = str_replace(Login::$p_account_dropdown, Login::$p_account_dropdown . " class=\"dropdown\"", $htmlPage);
                    
                    break;
            }
            unset($_SESSION["method"]);
            unset($_SESSION["success"]);
        }

        // login conditional structs
        if(Login::is_logged()) {
            Login::showElement(Login::$p_if_logged_open,     Login::$p_if_logged_close,     $htmlPage);
            Login::hideElement(Login::$p_if_not_logged_open, Login::$p_if_not_logged_close, $htmlPage);
        } else {
            Login::hideElement(Login::$p_if_logged_open,     Login::$p_if_logged_close,     $htmlPage);
            Login::showElement(Login::$p_if_not_logged_open, Login::$p_if_not_logged_close, $htmlPage);
        }

        if(Login::is_logged_admin()) {
            Login::showElement(Login::$p_if_logged_admin_open,     Login::$p_if_logged_admin_close,     $htmlPage);
            Login::hideElement(Login::$p_if_not_logged_admin_open, Login::$p_if_not_logged_admin_close, $htmlPage);
        } else {
            Login::hideElement(Login::$p_if_logged_admin_open,     Login::$p_if_logged_admin_close,     $htmlPage);
            Login::showElement(Login::$p_if_not_logged_admin_open, Login::$p_if_not_logged_admin_close, $htmlPage);
        }

        if(Login::is_logged()) {
            $htmlPage = str_replace(Login::$p_accountname, "<span id='accountName'>" . $_SESSION["accountname"] . "</span>", $htmlPage);
        } else {
            $htmlPage = str_replace(Login::$p_accountname, ""                                                              , $htmlPage);
        }

        Utils::feedbackCleanUp($htmlPage, "<loginfeedback/>", "<registerfeedbackpositive/>", "<registerfeedbacknegative/>", "<logoutfeedback/>");
    }
}

?>