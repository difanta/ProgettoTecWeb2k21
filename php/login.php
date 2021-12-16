<?php

include_once "db.php";
use DB\DBAccess;

if(isset($_POST["method"])) {
    if($_POST["method"] == "login") 
    {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if($connectionOk) {
            $result = $connection->get("SELECT * FROM Utente WHERE Utente.email = '$email'");
            if($result && ($result["password"] == $password)) {
                echo "success";
            } else {
                echo "wrong password or email";
            }
        } else {
            echo "connection error";
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
                echo "success";
            } else {
                echo "email already used or other errors";
            }
        } else {
            echo "connection error";
        }
    } 
    else 
    {
        echo "method is not login or register";
    }
}

?>