<?php

include_once "db.php";
use DB\DBAccess;

/* Input control feedback messages

// placeholders
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

    private static $p_if_logged = "iflogged";                           // show if logged
    private static $p_if_not_logged = "ifnotlogged";                    // show if not logged

    private static $p_if_login_success = "ifloginsuccess";              // show if login succeded
    private static $p_if_not_login_success = "ifnotloginsuccess";       // show if login failed

    private static $p_if_register_success = "ifregistersuccess";        // show if register succeded
    private static $p_if_not_register_success = "ifnotregistersuccess"; // show if register failed

    private static function display_account_buttons(&$htmlPage, $_is_logged) {
        $htmlPage = str_replace("placeholder", "style", $htmlPage);
        if($_is_logged) {
            $htmlPage = str_replace(Login::$p_if_logged     , ""              ,    $htmlPage); // show
            $htmlPage = str_replace(Login::$p_if_not_logged , "display: none" ,    $htmlPage); // hide
        } else {
            $htmlPage = str_replace(Login::$p_if_logged     , "display: none" ,    $htmlPage); // hide
            $htmlPage = str_replace(Login::$p_if_not_logged , ""              ,    $htmlPage); // show
        }
    }

    public static function is_logged() {
        return isset($_SESSION["login"]);

        // no need to verify with db since $_SESSION is not modifiable by the user

        /*$connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if($connectionOk) {
            $result = $connection->get("SELECT count(*) as num FROM Utente WHERE Utente.id = '" . $_SESSION["login"] . "'");
            if($result && ($result["num"] == 1)) 
            { return true; }
            else 
            { return false; }
        } else {
            echo "connection error";
        }*/
    }

    public static function is_logged_admin() {
        return (Login::is_logged() && $_SESSION["is_admin"] == true);

        // no need to verify with db since $_SESSION is not modifiable by the user

        /*$connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if($connectionOk) {
            $result = $connection->get("SELECT admin FROM Utente WHERE Utente.id = '" . $_SESSION["login"] . "'");
            if($result && ($result["admin"] == 1)) 
            { return true; }
            else 
            { return false; }
        } else {
            echo "connection error";
        }*/
    }

    public static function handle_login() {
        if(!Login::is_logged()) // if not logged try to login or register
        {
            if($_POST["method"] == "login") 
            {
                $email = $_POST["email"];
                $password = $_POST["password"];

                $connection = new DBAccess();
                $connectionOk = $connection->openDB();

                if($connectionOk) {
                    $result = $connection->get("SELECT * FROM Utente WHERE Utente.email = '$email'");
                    if($result && ($result["password"] == $password)) 
                         { $_SESSION["success"] = true  ; $_SESSION["login"] = $result["id"]; $_SESSION["is_admin"] = $result["admin"]; } 
                    else { $_SESSION["success"] = false ; }
                    $connection->closeConnection();
                } 
                else /* Connection not Ok */
                { $_SESSION["success"] = false; }

                if($_SESSION["success"] == false) 
                {   /* save data to avoid form reset */
                    $_SESSION["email"] = $_POST["email"];
                }
            } 
            elseif ($_POST["method"] == "register") 
            {
                $nome = $_POST["nome"];
                $cognome = $_POST["cognome"];
                $data_di_nascita = $_POST["data_di_nascita"];
                $email = $_POST["email"];
                $password = $_POST["password"];

                $connection = new DBAccess();
                $connectionOk = $connection->openDB();

                if($connectionOk) 
                {
                    if($connection->insert("INSERT INTO Utente(nome, cognome, data_di_nascita, email, password, admin)
                                            VALUES ('$nome', '$cognome', '$data_di_nascita', '$email', '$password', '0')"))
                         { $_SESSION["success"] = true  ; } 
                    else { $_SESSION["success"] = false ; }
                    $connection->closeConnection();
                } 
                else /* Connection not Ok */
                { $_SESSION["success"] = false; }

                if($_SESSION["success"] == false) 
                {   /* save data to avoid form reset */
                    $_SESSION["nome"] = $_POST["nome"];
                    $_SESSION["cognome"] = $_POST["cognome"];
                    $_SESSION["data_di_nascita"] = $_POST["data_di_nascita"];
                    $_SESSION["email"] = $_POST["email"];
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

    public static function set_login_contents(&$htmlPage) {
        if(isset($_SESSION["method"]))
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
                        $htmlPage = str_replace(Login::$p_if_login_success     , ""              , $htmlPage);
                        $htmlPage = str_replace(Login::$p_if_not_login_success , "display: none" , $htmlPage);
                    } else {
                        $htmlPage = str_replace(Login::$p_if_login_success     , "display: none" , $htmlPage);
                        $htmlPage = str_replace(Login::$p_if_not_login_success , ""              , $htmlPage);
                    }                 
                    
                    if(!$success) {
                        unset($_SESSION["email"]);
                    }

                    break;

                case 'register':
                    $htmlPage = str_replace(Login::$p_account_dropdown, Login::$p_account_dropdown . " class=\"dropdown\"", $htmlPage);
                    $htmlPage = str_replace(Login::$p_account_section,  Login::$p_account_section  . " slideOut\"",         $htmlPage);
                    $htmlPage = str_replace(Login::$p_signup_section,   Login::$p_signup_section   . " slideIn\"",          $htmlPage);
                    
                    if($success) {
                        $htmlPage = str_replace(Login::$p_if_register_success     , ""              , $htmlPage);
                        $htmlPage = str_replace(Login::$p_if_not_register_success , "display: none" , $htmlPage);
                    } else {
                        $htmlPage = str_replace(Login::$p_if_register_success     , "display: none" , $htmlPage);
                        $htmlPage = str_replace(Login::$p_if_not_register_success , ""              , $htmlPage);
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

        $htmlPage = str_replace(Login::$p_if_login_success        , "display: none" , $htmlPage);
        $htmlPage = str_replace(Login::$p_if_not_login_success    , ""              , $htmlPage);
        $htmlPage = str_replace(Login::$p_if_register_success     , "display: none" , $htmlPage);
        $htmlPage = str_replace(Login::$p_if_not_register_success , ""              , $htmlPage);

        // in all cases set login button status
        Login::display_account_buttons($htmlPage, Login::is_logged());
    }

}

?>