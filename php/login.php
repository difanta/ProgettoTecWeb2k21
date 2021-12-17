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

    private static $p_loginBtn = "<button title=\"login\" id=\"loginBtn\"";
    private static $p_signupBtn = "<button title=\"sign up\" id=\"signupBtn\"";
    private static $p_logoutBtn = "<button title=\"logout\" id=\"logout\" name=\"method\" value=\"logout\" form=\"login\"";
    private static $p_linkUserPage = "<a href=\"PaginaUtente.html\"";

    private static function display_account_buttons(&$htmlPage, $_is_logged) {
        if($_is_logged) {
            $htmlPage = str_replace(Login::$p_loginBtn,     Login::$p_loginBtn     . " style=\"display: none\"",   $htmlPage); // hide
            $htmlPage = str_replace(Login::$p_signupBtn,    Login::$p_signupBtn    . " style=\"display: none\"",   $htmlPage); // hide
        } else {
            $htmlPage = str_replace(Login::$p_logoutBtn,    Login::$p_logoutBtn    . " style=\"display: none\"",   $htmlPage); // hide
            $htmlPage = str_replace(Login::$p_linkUserPage, Login::$p_linkUserPage . " style=\"display: none\"",   $htmlPage); // hide
        }
    }

    public static function is_logged() {
        if(!isset($_SESSION["login"])) { return false; }

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if($connectionOk) {
            $result = $connection->get("SELECT count(*) as num FROM Utente WHERE Utente.id = '" . $_SESSION["login"] . "'");
            if($result && ($result["num"] == 1)) 
            { return true; }
            else 
            { return false; }
        } else {
            echo ("connection error");
        }
    }

    public static function is_logged_admin() {
        if(!isset($_SESSION["login"])) { return false; }

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if($connectionOk) {
            $result = $connection->get("SELECT admin FROM Utente WHERE Utente.id = '" . $_SESSION["login"] . "'");
            if($result && ($result["admin"] == 1)) 
            { return true; }
            else 
            { return false; }
        } else {
            echo ("connection error");
        }
    }

    public static function handle_login(&$htmlPage) {
        if(isset($_POST["method"])) // #1 if login method is defined go on (= if a login form button was clicked)
        {
            if(!Login::is_logged()) //    if not logged try to login or register
            {
                if($_POST["method"] == "login") 
                {
                    $email = $_POST["email"];
                    $password = $_POST["password"];

                    $connection = new DBAccess();
                    $connectionOk = $connection->openDB();

                    if($connectionOk) {
                        $result = $connection->get("SELECT * FROM Utente WHERE Utente.email = '$email'");
                        if($result && ($result["password"] == $password)) {
                            $_SESSION["login"] = $result["id"];
                        } else {
                            echo ("wrong password or email");
                        }
                        $connection->closeConnection();
                    } else {
                        echo ("connection error");
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

                    if($connectionOk) {
                        if($connection->insert("INSERT INTO Utente(nome, cognome, data_di_nascita, email, password, admin)
                                                VALUES ('$nome', '$cognome', '$data_di_nascita', '$email', '$password', '0')")) {
                        } else {
                            echo ("email already used"); // or other input errors, but the input should already be checked earlier
                        }
                        $connection->closeConnection();
                    } else {
                        echo ("connection error");
                    }
                }
            } else { // if logged you can only logout
                if($_POST["method"] == "logout") {
                    session_unset();
                    session_destroy();
                }
            }

            // Keep dropdown state (if #1)
            if       ($_POST["method"] == "login") {
                $htmlPage = str_replace(Login::$p_account_dropdown, Login::$p_account_dropdown . " class=\"dropdown\"", $htmlPage);
                $htmlPage = str_replace(Login::$p_account_section,  Login::$p_account_section  . " slideOut\"",         $htmlPage);
                $htmlPage = str_replace(Login::$p_login_section,    Login::$p_login_section    . " slideIn\"",          $htmlPage);
            } elseif ($_POST["method"] == "register") {
                $htmlPage = str_replace(Login::$p_account_dropdown, Login::$p_account_dropdown . " class=\"dropdown\"", $htmlPage);
                $htmlPage = str_replace(Login::$p_account_section,  Login::$p_account_section  . " slideOut\"",         $htmlPage);
                $htmlPage = str_replace(Login::$p_signup_section,   Login::$p_signup_section   . " slideIn\"",          $htmlPage);
            } elseif ($_POST["method"] == "logout") {
                $htmlPage = str_replace(Login::$p_account_dropdown, Login::$p_account_dropdown . " class=\"dropdown\"", $htmlPage);
            }

            // Set Button states (if #1)
            Login::display_account_buttons($htmlPage, Login::is_logged());
        }
    }

}

?>