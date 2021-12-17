<?php

namespace DB;

class DBAccess {
	private const HOST_DB = "127.0.0.1";
	private const DB_NAME = "tdifant";
	private const DB_USER = "tdifant";

	private $connection;
	
	public function openDB() {
		$this->connection = mysqli_connect(DBAccess::HOST_DB, DBAccess::DB_USER, trim(file_get_contents("../../../pwd_db_2021-22.txt")), DBAccess::DB_NAME);
		if(mysqli_errno($this->connection)) {
			return false;
		} else {
			return true;
		}
	}
	
	public function closeConnection() {
		mysqli_close($this->connection);
	}
	
	public function get($stringa) {
		$queryResult = mysqli_query($this->connection, $stringa);

		if(!$queryResult || mysqli_num_rows($queryResult) == 0) 
		{
			return null;
		} 
		elseif(mysqli_num_rows($queryResult) == 1) 
		{
            return mysqli_fetch_assoc($queryResult);
        } 
		else 
		{
			$result = array();
			while($row = mysqli_fetch_assoc($queryResult)) {
				array_push($result, $row);
			}
			return $result;
		}
	}

	public function insert($stringa) {
		$query = mysqli_query($this->connection, $stringa);

		if($query && mysqli_affected_rows($this->connection) > 0) {
			return true;
		} else {
			return false;
		}
	}
}


?>