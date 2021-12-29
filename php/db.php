<?php

namespace DB;

class DBAccess
{
    private const HOST_DB = "127.0.0.1";

    private $connection;

    public function openDB()
    {
        $config = json_decode(file_get_contents("../php/config.json"));
        $this->connection = mysqli_connect(DBAccess::HOST_DB, $config->db_username, trim(file_get_contents($config->pwd_filename)), $config->db_username);
        if (mysqli_errno($this->connection)) {
            return false;
        } else {
            return true;
        }
    }

    public function closeConnection()
    {
        $this->connection->close();
    }

    private function formatGetResult($queryResult)
    {
        if (!$queryResult || mysqli_num_rows($queryResult) == 0) {
            return null;
        } else {
            $result = array();
            while ($row = mysqli_fetch_assoc($queryResult)) {
                array_push($result, $row);
            }
            return $result;
        }
    }

    public function getFilmPopolari()
    {
        return $this->formatGetResult($this->connection->query("SELECT * from Film 
																join 
																(SELECT Film, count(*) as Likes FROM _Like group by Film) as _likes 
																on Film.id = _likes.Film 
																where Film.in_gara = '1' 
																order by Likes desc"));
    }

    public function getUser($email)
    {
        $stmt = $this->connection->prepare("SELECT * FROM Utente WHERE Utente.email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    public function getFilm($nomeFilm)
    {
        $stmt = $this->connection->prepare("SELECT * from Film where Film.nome = ?");
        $stmt->bind_param("s", $nomeFilm);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    public function getFilms($in_gara, $nomeFilm)
    {
        $query = "SELECT * from Film where Film.approvato = '1'";

        // add in gara filters
        switch ($in_gara) {
            case 'tutti':
                break;
            case 'gara':
                $query .= "and Film.in_gara = '1'";
                break;
            case 'noGara':
                $query .= "and Film.in_gara = '0'";
                break;
        }

        // add nome film filters
        if ($nomeFilm != "") {
            $query .= "and Film.nome like ?";
            $stmt = $this->connection->prepare($query);
            $nomeFilm = "%$nomeFilm%";
            $stmt->bind_param("s", $nomeFilm);
            $stmt->execute();
            return $this->formatGetResult($stmt->get_result());
        } else {
            return $this->formatGetResult($this->connection->query($query));
        }
    }

    public function getProiezioni($in_gara, $nomeFilm, $dataProiezione)
    {
        $query = "SELECT * from Film where Film.approvato = '1'";

        // add in gara filters
        switch ($in_gara) {
            case 'tutti':
                break;
            case 'gara':
                $query .= "and Film.in_gara = '1'";
                break;
            case 'noGara':
                $query .= "and Film.in_gara = '0'";
                break;
        }

        // add nome film filters
        if ($nomeFilm != "") {
            $query .= "and Film.nome like ?";
        }

        // finish query
        if ($dataProiezione != "") {
            $query = "SELECT *, Proiezione.id as pid, CAST(orario AS DATE) as data, TIME_FORMAT(CAST(orario AS TIME), '%H:%i') as ora 
						from Proiezione join ($query) as Film on Proiezione.film = Film.id 
						where CAST(orario AS DATE) = ? 
						order by orario asc";
        } else {
            $query = "SELECT *, Proiezione.id as pid, CAST(orario AS DATE) as data, TIME_FORMAT(CAST(orario AS TIME), '%H:%i') as ora 
						from Proiezione join ($query) as Film on Proiezione.film = Film.id 
						order by orario asc";
        }

        $stmt = $this->connection->prepare($query);
        if ($nomeFilm != "") {
            $nomeFilm = "%$nomeFilm%";
            $stmt->bind_param("s", $nomeFilm);
        }
        if ($dataProiezione != "") {
            $stmt->bind_param("s", $dataProiezione);
        }
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    public function getProiezione($proiezione) {
        $stmt = $this->connection->prepare("SELECT nome, orario 
                                            FROM Proiezione join Film on Proiezione.film = Film.id 
                                            WHERE Proiezione.id = ?");
        $stmt->bind_param("s", $proiezione);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    public function getNomiFilm()
    {
        return $this->formatGetResult($this->connection->query("SELECT nome from Film"));
    }

    private function checkInsert($queryResult)
    {
        if ($queryResult && mysqli_affected_rows($this->connection) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function insertUser($nome, $cognome, $data_di_nascita, $email, $password)
    {
        $stmt = $this->connection->prepare("	INSERT INTO Utente(nome, cognome, data_di_nascita, email, password, admin)
										VALUES (?, ?, ?, ?, ?, '0')");
        $stmt->bind_param("sssss", $nome, $cognome, $data_di_nascita, $email, $password);
        $stmt->execute();
        return $this->checkInsert($stmt->get_result());
    }

    public function insertContestFilm($titolo, $descrizione, $durata, $anno, $regista, $produttore, $cast)
    {
        $stmt = $this->connection->prepare("INSERT INTO Film(nome, descrizione, durata, anno, regista, produttore, cast, in_gara, approvato, candidatore)
										VALUES (?, ?, ?, ?, ?, ?, ?, '1', '0', ?)");
        $stmt->bind_param("ssiisssi", $titolo, $descrizione, $durata, $anno, $regista, $produttore, $cast, $_SESSION["login"]);
        $stmt->execute();
        return $this->checkInsert($stmt->get_result());
    }

    public function getUserById()
    {
        $stmt = $this->connection->prepare("SELECT * FROM Utente WHERE Utente.id = ?");
        $stmt->bind_param("s", $_SESSION["login"]);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    public function updateUser($nome, $cognome, $dataNascita, $email, $password)
    {
        $stmt = $this->connection->prepare("UPDATE Utente
                                            SET nome = ?, cognome= ?, data_di_nascita= ?, email= ?, password= ?
                                            where id= ?");
        $stmt->bind_param("ssssss", $nome, $cognome, $dataNascita, $email, $password, $_SESSION["login"]);
        $stmt->execute();
        return $this->checkInsert($stmt->get_result());
    }

    public function deleteUser()
    {
        $stmt = $this->connection->prepare("DELETE FROM Utente WHERE id= ?");
        $stmt->bind_param("s", $_SESSION["login"]);
        $stmt->execute();
        return $this->checkInsert($stmt->get_result());
    }

    public function getUserTickets()
    {
        $stmt = $this->connection->prepare("SELECT Film.nome, Biglietto.id, orario 
                                            FROM Utente join Biglietto on Utente.id=Biglietto.utente
                                            join Proiezione on Proiezione.id=Biglietto.proiezione
                                            join Film on Film.id= Proiezione.film 
                                            where Utente.id= ?");
        $stmt->bind_param("s", $_SESSION["login"]);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    public function insertTicket($proiezione)
    {
        $stmt = $this->connection->prepare("INSERT INTO Biglietto(utente, proiezione) 
                                            VALUES (? , ?)");
        $stmt->bind_param("ss", $_SESSION["login"], $proiezione);
        $stmt->execute();
        return $this->checkInsert($stmt->get_result());
    }

    public function getProiezioneRecap($proiezione){
        $stmt = $this->connection->prepare("SELECT nome, regista, CAST(orario AS DATE) as data, TIME_FORMAT(CAST(orario AS TIME), '%H:%i') as ora
                                            FROM Proiezione join Film on film 
                                            WHERE Proiezione.id= ? and Proiezione.film = Film.id");
        $stmt->bind_param("s", $proiezione);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }
}


?>