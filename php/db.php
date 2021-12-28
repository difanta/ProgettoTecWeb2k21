<?php

namespace DB;

class DBAccess {
	private const HOST_DB = "127.0.0.1";

	private $connection;
	
	public function openDB() {
		$config = json_decode(file_get_contents("config.json"));
		$this->connection = mysqli_connect(DBAccess::HOST_DB, $config->db_username, trim(file_get_contents($config->pwd_filename)), $config->db_username);
		if(mysqli_errno($this->connection)) {
			return false;
		} else {
			return true;
		}
	}
	
	public function closeConnection() {
		mysqli_close($this->connection);
	}

	private function formatGetResult($queryResult) {
		if(!$queryResult || mysqli_num_rows($queryResult) == 0) 
		{
			return null;
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
	
	public function get($stringa) {
		return formatGetResult(mysqli_query($this->connection, $stringa));
	}

	public function getUser($email) {
		$stmt = $this->connection->prepare("SELECT * FROM Utente WHERE Utente.email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		return formatGetResult($stmt->get_result());
	}

	public function getFilm($nomeFilm) {
		$stmt = $this->connection->prepare("SELECT * from Film where Film.nome = ?")
		$stmt->bind_param("s", $nomeFilm);
		$stmt->execute();
		return formatGetResult($stmt->get_result());
	}

	public function insert($stringa) {
		$query = mysqli_query($this->connection, $stringa);

		if($query && mysqli_affected_rows($this->connection) > 0) {
			return true;
		} else {
			return false;
		}
	}

    public function update($stringa){
        return $query = mysqli_query($this->connection, $stringa) === true;
    }
}


?>