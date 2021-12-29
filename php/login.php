<?php

include_once "db.php";
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

    private static $p_if_login_success_open = "<ifloginsuccess>";
    private static $p_if_login_success_close = "</ifloginsuccess>";
    
    private static $p_if_not_login_success_open = "<ifnotloginsuccess>";
    private static $p_if_not_login_success_close = "</ifnotloginsuccess>";
    
    private static $p_if_register_success_open = "<ifregistersuccess>";
    private static $p_if_register_success_close = "</ifregistersuccess>";

    private static $p_if_not_register_success_open = "<ifnotregistersuccess>";
    private static $p_if_not_register_success_close = "</ifnotregistersuccess>";

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
                $email    = $_POST["email"];
                $password = $_POST["password"];

                $connection   = new DBAccess();
                $connectionOk = $connection->openDB();

                $_SESSION["success"] = false;

                if($connectionOk) {
                    $result = $connection->getUser($email);
                    if($result && $result[0] && ($result[0]["password"] == $password)) 
                    {
                        $_SESSION["success"]  = true;
                        $_SESSION["login"]    = $result[0]["id"]; 
                        $_SESSION["is_admin"] = $result[0]["admin"]; 
                    } 
                    $connection->closeConnection();
                } 

                if($_SESSION["success"] == false) 
                {   /* save data to avoid form reset */
                    $_SESSION["email"] = $email;
                }
            } 
            elseif ($_POST["method"] == "register") 
            {
                $nome            = $_POST["nome"];
                $cognome         = $_POST["cognome"];
                $data_di_nascita = $_POST["data_di_nascita"];
                $email           = $_POST["email"];
                $password        = $_POST["password"];

                $connection = new DBAccess();
                $connectionOk = $connection->openDB();

                $_SESSION["success"] = false;

                if($connectionOk) 
                {
                    if(insertUser($nome, $cognome, $data_di_nascita, $email, $password))
                    { $_SESSION["success"] = true; } 
                    $connection->closeConnection();
                }

                if($_SESSION["success"] == false) 
                {   /* save data to avoid form reset */
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
            }
        }
        if(isset($_SESSION["success"])) {
            /* remember method if something happened*/
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
                    $htmlPage = str_replace(Login::$p_account_section,  Login::$p_account_section  . " slideOut\"",         $htmlPage);
                    $htmlPage = str_replace(Login::$p_login_section,    Login::$p_login_section    . " slideIn\"",          $htmlPage);
                    
                    if($success) {
                        Login::showElement(Login::$p_if_login_success_open,     Login::$p_if_login_success_close,     $htmlPage);
                        Login::hideElement(Login::$p_if_not_login_success_open, Login::$p_if_not_login_success_close, $htmlPage);
                    } else {
                        Login::hideElement(Login::$p_if_login_success_open,     Login::$p_if_login_success_close,     $htmlPage);
                        Login::showElement(Login::$p_if_not_login_success_open, Login::$p_if_not_login_success_close, $htmlPage);
                    }
                    
                    // only if it didn't succeed the variables were saved, look at handleLogin()
                    if(!$success) {
                        unset($_SESSION["email"]);
                    }

                    break;

                case 'register':
                    $htmlPage = str_replace(Login::$p_account_dropdown, Login::$p_account_dropdown . " class=\"dropdown\"", $htmlPage);
                    $htmlPage = str_replace(Login::$p_account_section,  Login::$p_account_section  . " slideOut\"",         $htmlPage);
                    $htmlPage = str_replace(Login::$p_signup_section,   Login::$p_signup_section   . " slideIn\"",          $htmlPage);
                    
                    if($success) {
                        Login::showElement(Login::$p_if_register_success_open,     Login::$p_if_register_success_close,     $htmlPage);
                        Login::hideElement(Login::$p_if_not_register_success_open, Login::$p_if_not_register_success_close, $htmlPage);
                    } else {
                        Login::hideElement(Login::$p_if_register_success_open,     Login::$p_if_register_success_close,     $htmlPage);
                        Login::showElement(Login::$p_if_not_register_success_open, Login::$p_if_not_register_success_close, $htmlPage);
                    }

                    if(!$success) {
                        unset($_SESSION["nome"]);
                        unset($_SESSION["cognome"]);
                        unset($_SESSION["data_di_nascita"]);
                        unset($_SESSION["email"]);
                    }

                    break;

                case 'logout':
                    $htmlPage = str_replace(Login::$p_account_dropdown, Login::$p_account_dropdown . " class=\"dropdown\"", $htmlPage);
                    
                    break;
            }
            unset($_SESSION["method"]);
            unset($_SESSION["success"]);
        }

        // default: show not_success items
        Login::hideElement(Login::$p_if_login_success_open,        Login::$p_if_login_success_close,        $htmlPage);
        Login::showElement(Login::$p_if_not_login_success_open,    Login::$p_if_not_login_success_close,    $htmlPage);
        Login::hideElement(Login::$p_if_register_success_open,     Login::$p_if_register_success_close,     $htmlPage);
        Login::showElement(Login::$p_if_not_register_success_open, Login::$p_if_not_register_success_close, $htmlPage);

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
    }
}

?>