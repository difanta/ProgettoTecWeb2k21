<?php

include_once "db.php";
use DB\DBAccess;

class FS {

    const EXTS = array(".jpg", ".png");

    static function writeImage($filmName, $ext, $data) 
    {
        if(!in_array($ext, FS::EXTS)) { return false; }

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if($connectionOk) {
            $result = $connection->getFilm($filmName);
            $connection->closeConnection();
            if($result && $result[0]) {
                $path = "../images/film/film" . $result[0]["id"] . $ext;
                file_put_contents($path, $data);
                return $path;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    static function moveImage($filmName, $tmpPath, $ext) {
        if(!in_array($ext, FS::EXTS)) { return false; }

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if($connectionOk) {
            $result = $connection->getFilm($filmName);
            $connection->closeConnection();
            if($result && $result[0]) {
                $path = "../images/film/film" . $result[0]["id"] . $ext;
                return move_uploaded_file($tmpPath, $path);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    static function findImage($filmName) {
        $path = false;

        $connection = new DBAccess();
        $connectionOk = $connection->openDB();

        if($connectionOk) {
            $result = $connection->getFilm($filmName);
            $connection->closeConnection();
            if($result && $result[0]) {
                foreach(FS::EXTS as $ext) {
                    if(file_exists("../images/film/film" . $result[0]["id"] . $ext)) { 
                        $path =    "../images/film/film" . $result[0]["id"] . $ext;
                        break;
                    }
                }
            }
        }
        return $path;
    }

}

?>