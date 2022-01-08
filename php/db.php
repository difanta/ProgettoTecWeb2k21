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

    public function getUserByEmail($email)
    {
        $stmt = $this->connection->prepare("SELECT * FROM Utente WHERE Utente.email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    public function getLike($utente, $nomeFilm) {
        $stmt = $this->connection->prepare("SELECT count(*) as num 
                                            FROM _Like 
                                            WHERE utente = ? and film = (SELECT id from Film where Film.nome = ?)");
        $stmt->bind_param("ss", $utente, $nomeFilm);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    public function insertLike($utente, $nomeFilm) {
        $stmt = $this->connection->prepare("INSERT INTO _Like(utente, film) VALUES
                                            (?, (SELECT id from Film where Film.nome = ?))");
        $stmt->bind_param("ss", $utente, $nomeFilm);
        return $stmt->execute();
    }

    public function removeLike($utente, $nomeFilm) {
        $stmt = $this->connection->prepare("DELETE from _Like
                                            where utente = ? and film = (SELECT id from Film where Film.nome = ?)");
        $stmt->bind_param("ss", $utente, $nomeFilm);
        return $stmt->execute();
    }

    public function getFilm($nomeFilm)
    {
        $stmt = $this->connection->prepare("SELECT * from Film where Film.nome = ?");
        $stmt->bind_param("s", $nomeFilm);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    public function getFilmsApprovati($in_gara, $nomeFilm)
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

        $types = "";
        $array_of_values = array();

        $stmt = $this->connection->prepare($query);

        if ($nomeFilm != "") {
            $types .= "s";
            array_push($array_of_values, "%$nomeFilm%");
        }
        if ($dataProiezione != "") {
            $types .= "s";
            array_push($array_of_values, $dataProiezione);
        }
        if($types != "") {
            $stmt->bind_param($types, ...$array_of_values);
        }
        
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    public function getProiezione($proiezione)
    {
        $stmt = $this->connection->prepare("SELECT nome, orario 
                                            FROM Proiezione join Film on Proiezione.film = Film.id 
                                            WHERE Proiezione.id = ?");
        $stmt->bind_param("i", $proiezione);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    public function getNomiFilm()
    {
        return $this->formatGetResult($this->connection->query("SELECT nome from Film"));
    }

    public function insertUser($nome, $cognome, $data_di_nascita, $email, $password)
    {
        $stmt = $this->connection->prepare("INSERT INTO Utente(nome, cognome, data_di_nascita, email, password, admin)
										    VALUES (?, ?, ?, ?, ?, '0')");
        $stmt->bind_param("sssss", $nome, $cognome, $data_di_nascita, $email, $password);
        return $stmt->execute();
    }

    /**
     * @used_in adminListaFilm.php
    */

    function addFilm($nomeFilm, $produttore, $regista, $anno, $durata, $descrizioneFilm, $cast, $inGara, $approvato) {
        if($inGara) { $inGara = 1; }
        else { $inGara = 0; }

        if($approvato) { $approvato = 1; }
        else { $approvato = 0; }

        $stmt = $this->connection->prepare("INSERT INTO Film(nome, produttore, regista, anno, durata, descrizione, cast, in_gara, approvato) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $nomeFilm, $produttore, $regista, $anno, $durata, $descrizioneFilm, $cast, $inGara, $approvato);
        return $stmt->execute();
    }

    public function modifyFilm($oldNomeFilm, $newNomeFilm, $produttore, $regista, $anno, $durata, $descrizioneFilm, $cast, $inGara, $approvato) {
        if($inGara) { $inGara = 1; }
        else { $inGara = 0; }

        if($approvato) { $approvato = 1; }
        else { $approvato = 0; }

        $stmt = $this->connection->prepare("UPDATE Film
                                            set nome = ?, produttore = ?, regista = ?, anno = ?, durata = ?, descrizione = ?, cast = ?, in_gara = ?, approvato = ?  
                                            where Film.nome = ?");
        $stmt->bind_param("ssssssssss", $newNomeFilm, $produttore, $regista, $anno, $durata, $descrizioneFilm, $cast, $inGara, $approvato, $oldNomeFilm);
        return $stmt->execute();
    }

    public function deleteFilm($nomeFilm) {
        $stmt = $this->connection->prepare("DELETE from Film where Film.nome = ?");
        $stmt->bind_param("s", $nomeFilm);
        return $stmt->execute();
    }

    /**
     * @used_in adminProiezioni.php
     */

    public function addProiezione($nomeFilm, $data) {
        $stmt = $this->connection->prepare("INSERT INTO Proiezione(orario, film) 
                                            VALUES (?, (SELECT id from Film where Film.nome = ?))");
        $stmt->bind_param("ss", $data, $nomeFilm);
        return $stmt->execute();
    }

    public function modifyProiezione($idProiezione, $nomeFilm, $data) {
        $stmt = $this->connection->prepare("UPDATE Proiezione
                                            set orario = ?, film = (SELECT id from Film where Film.nome = ?)
                                            where Proiezione.id = ?");
        $stmt->bind_param("ssi", $data, $nomeFilm, $idProiezione);
        return $stmt->execute();
    }

    public function deleteProiezione($idProiezione) {
        $stmt = $this->connection->prepare("DELETE from Proiezione where Proiezione.id = ?");
        $stmt->bind_param("i", $idProiezione);
        return $stmt->execute();
    }

    /**
     * @used_in contest.php
     */
    public function insertContestFilm($titolo, $descrizione, $durata, $anno, $regista, $produttore, $cast)
    {
        $stmt = $this->connection->prepare("INSERT INTO Film(nome, descrizione, durata, anno, regista, produttore, cast, in_gara, approvato, candidatore)
										VALUES (?, ?, ?, ?, ?, ?, ?, '1', '0', ?)");
        $stmt->bind_param("ssiisssi", $titolo, $descrizione, $durata, $anno, $regista, $produttore, $cast, $_SESSION["login"]);
        return $stmt->execute();
    }

    /**
     * @used_in paginaUtente.php
     */
    public function getUser()
    {
        $stmt = $this->connection->prepare("SELECT * FROM Utente WHERE Utente.id = ?");
        $stmt->bind_param("i", $_SESSION["login"]);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    /**
     * @used_in adminUtenti.php
     */
    public function getUserById($id)
    {
        $stmt = $this->connection->prepare("SELECT * FROM Utente WHERE Utente.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    /**
     * @used_in paginaUtente.php
     */
    public function updateUser($nome, $cognome, $dataNascita, $email, $password)
    {
        $stmt = $this->connection->prepare("UPDATE Utente
                                            SET nome = ?, cognome= ?, data_di_nascita= ?, email= ?, password= ?
                                            where id= ?");
        $stmt->bind_param("sssssi", $nome, $cognome, $dataNascita, $email, $password, $_SESSION["login"]);
        return $stmt->execute();
    }

    /**
     * @used_in paginaUtente.php
     */
    public function deleteUser()
    {
        $stmt = $this->connection->prepare("DELETE FROM Utente WHERE id= ?");
        $stmt->bind_param("i", $_SESSION["login"]);
        return $stmt->execute();
    }

    /**
     * @used_in paginaUtente.php
     */
    public function getUserTickets()
    {
        $stmt = $this->connection->prepare("SELECT Film.nome, Biglietto.id, CAST(orario AS DATE) as data, TIME_FORMAT(CAST(orario AS TIME), '%H:%i') as ora 
                                            FROM Utente join Biglietto on Utente.id=Biglietto.utente
                                            join Proiezione on Proiezione.id=Biglietto.proiezione
                                            join Film on Film.id= Proiezione.film 
                                            where Utente.id= ?");
        $stmt->bind_param("i", $_SESSION["login"]);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    /**
     * @used_in acquistoBiglietti.php
     */
    public function insertTicket($proiezione)
    {
        $stmt = $this->connection->prepare("INSERT INTO Biglietto(utente, proiezione) 
                                            VALUES (? , ?)");
        $stmt->bind_param("ii", $_SESSION["login"], $proiezione);
        return $stmt->execute();
    }

    /**
     * @used_in acquistoBiglietti.php
     */
    public function getProiezioneRecap($proiezione)
    {
        $stmt = $this->connection->prepare("SELECT nome, regista, CAST(orario AS DATE) as data, TIME_FORMAT(CAST(orario AS TIME), '%H:%i') as ora
                                            FROM Proiezione join Film on film 
                                            WHERE Proiezione.id= ? and Proiezione.film = Film.id");
        $stmt->bind_param("i", $proiezione);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    /**
     * @used_in adminCandidature.php
     */
    public function getCandidatureAndEmail($filter_candidatura)
    {

        $query = "SELECT Film.nome, Film.descrizione, Film.durata, Film.anno, Film.regista, Film.produttore, Film.cast, Utente.email
                  FROM Film Join Utente on (Film.candidatore = Utente.id)
                  WHERE candidatore IS NOT NULL";

        // add filters
        switch ($filter_candidatura) {
            case 'Sospesa':
                $query .= " and Film.approvato = 0";
                break;
            case 'Approvata':
                $query .= " and Film.approvato = 1";
                break;
        }
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    /**
     * @used_in adminCandidature.php
     */
    public function deleteCandidatura($titolo)
    {
        $stmt = $this->connection->prepare("DELETE FROM Film WHERE nome= ?");
        $stmt->bind_param("s", $titolo);
        return $stmt->execute();
    }

    /**
     * @used_in adminCandidature.php
     */
    public function approvaCandidatura($titolo)
    {
        $stmt = $this->connection->prepare("UPDATE Film
                                            SET approvato = '1'
                                            WHERE nome= ?");
        $stmt->bind_param("s", $titolo);
        return $stmt->execute();
    }

    /**
     * @used_in admin.php
     */
    public function getNoUtenti()
    {
        return $this->formatGetResult($this->connection->query("SELECT COUNT(id) as no FROM Utente"));
    }

    /**
     * @used_in admin.php
     */
    public function getNoBiglietti()
    {
        return $this->formatGetResult($this->connection->query("SELECT COUNT(id) as no FROM Biglietto"));
    }

    /**
     * @used_in admin.php
     */
    public function getNoLike()
    {
        return $this->formatGetResult($this->connection->query("SELECT COUNT(*) as no FROM _Like"));
    }

    /**
     * @used_in admin.php
     */
    public function getMediaBigliettiPerProieizione()
    {
        return $this->formatGetResult($this->connection->query("")); // TODO
    }

    /**
     * @used_in adminUtenti.php
     */
    public function getUserTicketsByEmail($email)
    {
        $stmt = $this->connection->prepare("SELECT Film.nome, Biglietto.id, CAST(orario AS DATE) as data, TIME_FORMAT(CAST(orario AS TIME), '%H:%i') as ora 
                                            FROM Utente join Biglietto on Utente.id=Biglietto.utente
                                            join Proiezione on Proiezione.id=Biglietto.proiezione
                                            join Film on Film.id= Proiezione.film 
                                            where Utente.email= ?");
        $stmt->bind_param("i", $email);
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }

    /**
     * @used_in adminUtente.php
     */
    public function getEmailUtenti(){
        $stmt = $this->connection->prepare("SELECT email FROM Utente");
        $stmt->execute();
        return $this->formatGetResult($stmt->get_result());
    }
}


?>