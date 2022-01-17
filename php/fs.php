<?php

include_once "db.php";
use DB\DBAccess;

class FS {

    const EXTS = array("jpg", "png");

    static function moveImage($filmName, $tmpPath, $ext, $connection=null) {
        if(!in_array($ext, FS::EXTS)) { $_SESSION["feedback"] = " wrong extension $ext"; return false; }

        $closeConnection = false;
        $connectionOk = true;
        if(!$connection) {
            $closeConnection = true;
            $connection = new DBAccess();
            $connectionOk = $connection->openDB();
        }

        if($connectionOk) {
            $result = $connection->getFilm($filmName);
            if($closeConnection) { $connection->closeConnection(); }
            if($result && $result[0]) {
                $path = "../images/film/film" . $result[0]["id"] . "." . $ext;
                $_SESSION["feedback"] = "path error";
                return move_uploaded_file($tmpPath, $path);
            } else {
                $_SESSION["feedback"] = "film name not found";
            }
        } else {
            $_SESSION["feedback"] = "connection failed";
        }
        return false;
    }

    static function findImage($filmName, $connection=null) {
        $closeConnection = false;
        $connectionOk = true;
        if(!$connection) {
            $closeConnection = true;
            $connection = new DBAccess();
            $connectionOk = $connection->openDB();
        }

        if($connectionOk) {
            $result = $connection->getFilm($filmName);
            if($closeConnection) { $connection->closeConnection(); }
            if($result && $result[0]) {
                foreach(FS::EXTS as $ext) {
                    if(file_exists("../images/film/film" . $result[0]["id"] . "." . $ext)) { 
                        return     "../images/film/film" . $result[0]["id"] . "." . $ext;
                    }
                }
            }
        }
        return false;
    }

}

?>