-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Feb 01, 2022 alle 16:03
-- Versione del server: 10.3.32-MariaDB-0ubuntu0.20.04.1
-- Versione PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tdifant`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `Biglietto`
--

CREATE TABLE `Biglietto` (
  `id` int(11) NOT NULL,
  `utente` int(11) NOT NULL,
  `proiezione` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Biglietto`
--

INSERT INTO `Biglietto` (`id`, `utente`, `proiezione`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 6),
(4, 3, 12),
(5, 3, 20),
(6, 4, 14),
(7, 4, 16),
(8, 5, 11),
(9, 5, 4),
(10, 6, 5),
(11, 6, 6),
(12, 6, 7),
(13, 6, 9),
(14, 6, 14),
(15, 6, 16),
(16, 7, 12),
(17, 7, 13),
(18, 8, 1),
(19, 8, 2),
(20, 8, 3),
(21, 9, 16),
(22, 9, 17),
(23, 9, 19),
(24, 11, 7),
(25, 11, 8),
(26, 11, 12),
(27, 12, 11),
(28, 12, 15),
(29, 12, 16),
(30, 13, 17),
(31, 13, 12),
(32, 13, 13),
(33, 13, 16),
(34, 14, 9),
(35, 14, 11),
(36, 14, 7),
(37, 14, 2),
(38, 14, 1),
(39, 14, 3),
(40, 14, 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `Film`
--

CREATE TABLE `Film` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descrizione` text NOT NULL,
  `durata` int(11) NOT NULL,
  `anno` smallint(6) NOT NULL,
  `regista` varchar(50) NOT NULL,
  `produttore` varchar(50) NOT NULL,
  `cast` text NOT NULL,
  `in_gara` tinyint(4) NOT NULL,
  `approvato` tinyint(4) NOT NULL,
  `candidatore` int(11) DEFAULT NULL,
  `alt` varchar(125) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Film`
--

INSERT INTO `Film` (`id`, `nome`, `descrizione`, `durata`, `anno`, `regista`, `produttore`, `cast`, `in_gara`, `approvato`, `candidatore`, `alt`) VALUES
(1, 'Gilberto Filé - L\'addio', 'Due grandi amici del celebre professore Unipd celebrano il suo ritiro ricordando vari momenti classici della vita dell\'egregio professor Filé. Tra gossip e storie piccanti, il percorso didattico del professore storico di Informatica Unipd viene descritto con zelo e precisione senza precedenti.', 120, 2020, 'Francesco Ranzato, Alessandro Sperduti', 'Unipd', 'Vari membri dell\'università di Padova', 1, 1, 11, 'Filé assieme ad altri professori mentre presiede una proclamazione di laurea in Informatica.'),
(2, 'Il Padrino', 'Il film è ambientato a New York in pieno dopoguerra, tra la fine degli anni 1940 e la prima metà degli anni 1950. Il protagonista è don Vito Corleone, capo di una famiglia mafiosa divenuta col tempo una delle più potenti organizzazioni criminali della Grande Mela, grazie al rispetto e all\'onorabilità ottenute dal patriarca e dai figli coinvolti nelle attività malavitose. Quando don Vito rimane vittima di un attentato da parte di un boss rivale, il figlio Michael Corleone comincia l\'ascesa nell\'impero criminale della famiglia, fino a diventare il nuovo \'padrino\'.', 175, 1972, 'Francis Ford Coppola', 'Albert S. Ruddy', 'Marlon Brando, Al Pacino, James Caan', 0, 1, NULL, 'Lo sguardo potente di Don Vito Corleone'),
(3, 'La favolosa storia di pelle d\'asino', 'Il curatissimo ed eccentrico adattamento di una classica favola francese. Catherine Deneuve è una principessa costretta a vestire i panni di una sguattera per evitare di sposare il re, ovvero il proprio padre.', 90, 1970, 'Jacques Demy', 'paris cinema', 'Catherine Deneuve; Jean Marais; Jacques Perrin', 1, 0, 13, ''),
(4, 'Selfie', 'Gli adolescenti Pietro e Alessandro sono due amici inseparabili di Napoli che accettano la proposta del regista di riprendersi con un iPhone, commentando quotidianamente le proprie esperienze di vita, le loro amicizie, il loro quartiere e una tragedia condivisa.', 78, 2019, 'Agostino Ferrente', 'Berdugo productions', 'Alessandro Antonelli; Pietro Orlando', 1, 0, 14, ''),
(5, 'Les parapluies de Cherbourg', 'Geneviève (Catherine Deneuve) vive a Cherbourg con la madre, che gestisce un negozio di ombrelli. Ama Guy, giovane meccanico, ma il loro rapporto è interrotto dal richiamo alle armi per la guerra d’Algeria.', 92, 1964, 'Catherine Deneuve', 'paris cinema', 'Catherine Deneuve; Nino Castelnuovo; Anne Vernon', 1, 0, 10, ''),
(6, 'Involuntary', 'Cinque episodi intrecciati. Storie di tragicomica quotidianità che mettono a fuoco i condizionamenti sociali a cui è sottoposto l’individuo. Camera fissa, lunghi piani sequenza e profondità di campo sono scelte stilistiche funzionali all’osservazione delle dinamiche del gruppo.', 98, 2008, 'Ruben Ostlund', 'Chigi films', 'Maria Lundqvist; Leif Edlund; Vera Vitali', 1, 1, 7, 'Una maestra ed un\'alunna guardano un\'illusione ottica di due linee apparentemente uguali.'),
(7, 'First Cow', 'Intorno al 1820, un cuoco solitario e taciturno in viaggio verso l’Oregon incontra un immigrato cinese anch’egli in cerca di fortuna. Presto i due si uniranno in un piano pericoloso per rubare il latte della preziosa mucca Jersey del ricco proprietario terriero – la prima e l’unica del territorio.', 135, 2019, 'Kelly Reichardt', 'mubi productions', 'John Magaro; Orion Lee', 1, 1, 12, 'Una mucca viene trasportata su una zattera su un fiume.'),
(8, 'Anne at 13,000 ft', 'Anne ha una vita apparentemente normale. Ma dopo aver fatto paracadutismo per l’addio al nubilato della sua migliore amica, qualcosa cambia: la pressione della quotidianità minaccia di sommergerla e quando conosce Matt, comincia ad andare oltre ciò che è socialmente accettabile.', 75, 2019, 'Kazir Radwanski', 'Sergio Mattarella', 'Deragh Campbell; Matt Johnson', 1, 0, 9, ''),
(9, 'Zombi Child', 'Haiti, 1962. Un uomo viene fatto resuscitare solo per essere mandato a lavorare nelle piantagioni di canna da zucchero. 55 anni dopo a Parigi, una ragazza haitiana confessa un antico segreto di famiglia a un gruppo di nuove amiche, provocando conseguenze irreparabili.', 103, 2019, 'Bertrand Bonello', 'Sergio Mattarella', 'Louise Labeque; Wislanda Louimat', 1, 0, 9, ''),
(10, 'Friends and strangers', 'Ray e Alice si incontrano per caso e decidono di andare in campeggio. Durante il viaggio, una serie di goffi approcci romantici causano un’imbarazzante tensione tra i due. Di ritorno a Sydney, le disavventure di Ray continuano mentre cerca di convincere uno strano cliente ad affidargli un lavoro.', 84, 2021, 'James Vaughan', 'Sergio Mattarella', 'Emma Diaz; Greg Zimbulis', 1, 0, 9, ''),
(11, 'Racconto d\'inverno', 'In vacanza in Bretagna, Félicie incontra Charles e vivono una storia d’amore importante. Per un lapsus, però, i due non riusciranno a tenersi in contatto. Cinque anni dopo Félicie ha una figlia, avuta da Charles, vive con la madre e lavora come parrucchiera. Ma non ha ancora dimenticato Charles.', 109, 1992, 'Eric Rohmer', 'users productions', 'Charlotte Very; Frederic Van Den Driessche', 1, 1, 1, 'Una coppia si scambia uno sguardo d\'amore mentre abbracciano una bambina.'),
(12, 'Un coteau dans le coeur', 'Parigi, 1979, Anne ha fatto carriera producendo film porno, Per riconquistare la fiducia del suo compagno, decide di finanziare un film molto più ambizioso, ma un misterioso assassino uccide uno degli attori. Tuttavia, Anne decide di continuare le riprese senza sapere se il killer agirà di nuovo.', 102, 2018, 'Yann Gonzalez', 'users production', 'Vanessa Paradis; Kate Moran; Nicolas Maury', 1, 0, 1, ''),
(13, 'Shiva Baby', 'L’universitaria Danielle affronta una serie di incontri imbarazzanti durante una shiva, una riunione funebre della tradizione ebraica. Tra parenti dispotici, è irritata dalla ricomparsa di un’ex fidanzata e del suo sugar daddy segreto, che arriva inaspettatamente con la moglie e il figlio.', 77, 2020, 'Emma Seligman', 'mubi productions', 'Rachel Sennott', 1, 1, 12, 'Una ragazza con sguardo infastidito mentre tiene uno stuzzichino.'),
(14, 'Le notti di Cabiria', 'Una gracile prostituta si aggira per le strade di Roma in cerca del vero amore ma trova solamente delusioni.', 110, 1957, 'Federico Fellini', 'De Laurentiis', 'Giulietta Masina; Francois Perier', 0, 1, NULL, 'Cabiria, vestita con una pelliccia, sorride in una strada romana.'),
(15, 'Nuovo cinema Paradiso', 'Un regista ricorda la propria infanza in un piccolo villagio italiano in cui è cresciuto innamorandosi dell\'arte del cinema e stringedo amicizia con il proiezionista del cinema locale.', 173, 1988, 'Giuseppe Tornatore', 'Cristaldifilms', 'Salvatore Cascio; Philippe Noiret', 0, 1, NULL, 'Un bambino guarda affascinato la pellicola di un film nella sala di proiezione.'),
(16, 'Midsommar', 'Una coppia giunge in Svezia per la tradizionale festa di mezza estate che si celebra sul posto. Quella che doveva essere una vacanza paradisiaca si trasformerà presto in una violenta e curiosa competizione per via di un culto pagano.', 148, 2019, 'Ari Aster', 'B-Reel Films', 'Florence Pugh; Jack Reynor; Villhelm Blomgren', 0, 1, NULL, 'Due donne in abiti tipici svedesi stanno bevendo, una è spaventata.'),
(17, 'La parola ai giurati', 'Un membro della giuria tenta di impedire un errore giudiziario costringendo i suoi colleghi a riesaminare le prove.', 96, 1957, 'Sidney Lumet', 'Big productions', 'Henry Fonda; Lee J. Cobb', 0, 1, NULL, 'I membri di una giuria, seduti attorno ad un tavolo, guardano spazientiti lo spettatore.'),
(18, 'Il dottor Stranamore', 'Un generale pazzo avvia una percorso che conduce all\'olocausto nucleare, mentre un Comando Strategico di numerosi politici e generali cerca affannosamente di fermarlo.', 95, 1964, 'Stanley Kubrick', 'Columbia Pictures', 'Peter Sellers', 0, 1, NULL, 'Un uomo con lo sguardo da matto con sullo sfondo dei generali miltari e una mappa del mondo.'),
(19, 'The Florida Project', 'La trama si svolge nel corso di un\'estate e segue la precoce bambina di sei anni Moonee che combina monellerie e si lancia in avventure con i suoi improvvisati amici di giochi e costruisce il legame con la sua madre ribelle ma affezionata, il tutto mentre vivono all\'ombra del Walt Disney World.', 111, 2017, 'Sean Baker', 'Cre Film', 'Brooklynn Prince; Bria Vinaite', 0, 1, NULL, 'Una ragazza e due bambini sono seduti ad un tavolo. Lei fuma, loro mangiano cibo d\'asporto.'),
(20, 'Arrival', 'Una linguista viene reclutata dall\'esercito per comunicare con forme di vita aliene dopo che dodici misteriose navicelle spaziali sono atterrate sul nostro pianeta.', 116, 2016, 'Denis Villeneuve', 'Lava Bear Films', 'Amy Adams; Jeremy Renner', 0, 1, NULL, 'Delle persona in tuta isolante si avvicinano ad un varco rettangolare di una grotta.');

-- --------------------------------------------------------

--
-- Struttura della tabella `Proiezione`
--

CREATE TABLE `Proiezione` (
  `id` int(11) NOT NULL,
  `orario` datetime NOT NULL,
  `film` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Proiezione`
--

INSERT INTO `Proiezione` (`id`, `orario`, `film`) VALUES
(1, '2022-09-12 15:00:00', 7),
(2, '2022-09-12 18:30:00', 11),
(3, '2022-09-12 21:30:00', 1),
(4, '2022-09-12 23:45:00', 6),
(5, '2022-09-13 18:00:00', 6),
(6, '2022-09-13 21:00:00', 7),
(7, '2022-09-13 23:30:00', 13),
(8, '2022-09-14 18:00:00', 11),
(9, '2022-09-14 21:00:00', 11),
(10, '2022-09-15 18:00:00', 13),
(11, '2022-09-15 21:00:00', 1),
(12, '2022-09-16 18:00:00', 14),
(13, '2022-09-16 21:00:00', 19),
(14, '2022-09-16 23:30:00', 16),
(15, '2022-09-17 18:00:00', 17),
(16, '2022-09-17 21:00:00', 18),
(17, '2022-09-17 23:15:00', 19),
(18, '2022-09-18 16:00:00', 20),
(19, '2022-09-18 18:30:00', 16),
(20, '2022-09-18 21:30:00', 15),
(21, '2022-09-18 23:00:00', 20);

-- --------------------------------------------------------

--
-- Struttura della tabella `Utente`
--

CREATE TABLE `Utente` (
  `id` int(11) NOT NULL,
  `nome` varchar(20) NOT NULL,
  `cognome` varchar(20) NOT NULL,
  `data_di_nascita` date NOT NULL,
  `email` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL,
  `admin` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Utente`
--

INSERT INTO `Utente` (`id`, `nome`, `cognome`, `data_di_nascita`, `email`, `password`, `admin`) VALUES
(1, 'utente', 'utente', '2000-01-01', 'user', 'user', 0),
(2, 'admin', 'admin', '2000-01-01', 'admin', 'admin', 1),
(3, 'alberto', 'tecweb', '2000-02-02', 'alberto@unipd.it', 'alberto123', 1),
(4, 'tommaso', 'tecweb', '2000-04-07', 'tommaso@unipd.it', 'tommaso123', 1),
(5, 'luca', 'tecweb', '2000-07-04', 'luca@unipd.it', 'luca123', 1),
(6, 'giovanni', 'tecweb', '2000-09-06', 'giovanni@unipd.it', 'giovanni123', 1),
(7, 'mario', 'draghi', '1964-02-02', 'ildragone@.palazzochigi.it', 'mario123', 0),
(8, 'giorgia', 'meloni', '1973-04-07', 'lagiorgia@fdi.it', 'giorgia123', 0),
(9, 'sergio', 'mattarella', '1955-07-04', 'sergiomattarella@quirinale.it', 'sergio123', 0),
(10, 'virginia', 'raggi', '1979-09-06', 'virgy79@comunediroma.it', 'virginia123', 0),
(11, 'ombretta', 'gaggi', '1989-07-04', 'ombretta@unipd.it', 'ombretta123', 0),
(12, 'daniela', 'mapelli', '1982-09-06', 'dmap@unipd.it', 'daniela123', 0),
(13, 'jacques', 'demy', '1945-12-03', 'jacquesdemy@pariscinema.fr', 'jacques123', 0),
(14, 'agostino', 'ferrente', '1992-11-11', 'ferri@gmail.com', 'agostino123', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `_Like`
--

CREATE TABLE `_Like` (
  `utente` int(11) NOT NULL,
  `film` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `_Like`
--

INSERT INTO `_Like` (`utente`, `film`) VALUES
(1, 1),
(1, 3),
(1, 6),
(1, 14),
(1, 15),
(1, 17),
(1, 18),
(3, 1),
(3, 2),
(3, 13),
(4, 1),
(4, 5),
(4, 13),
(4, 17),
(5, 1),
(5, 13),
(6, 13),
(7, 1),
(7, 4),
(7, 6),
(7, 19),
(8, 1),
(8, 19),
(9, 1),
(9, 6),
(9, 12),
(9, 19),
(10, 13),
(10, 19),
(11, 16),
(12, 1),
(12, 6),
(12, 16),
(13, 6),
(13, 16),
(14, 6);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `Biglietto`
--
ALTER TABLE `Biglietto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utente` (`utente`),
  ADD KEY `proiezione` (`proiezione`);

--
-- Indici per le tabelle `Film`
--
ALTER TABLE `Film`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD KEY `candidatore` (`candidatore`);

--
-- Indici per le tabelle `Proiezione`
--
ALTER TABLE `Proiezione`
  ADD PRIMARY KEY (`id`),
  ADD KEY `film` (`film`);

--
-- Indici per le tabelle `Utente`
--
ALTER TABLE `Utente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indici per le tabelle `_Like`
--
ALTER TABLE `_Like`
  ADD PRIMARY KEY (`utente`,`film`),
  ADD KEY `film` (`film`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `Biglietto`
--
ALTER TABLE `Biglietto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT per la tabella `Film`
--
ALTER TABLE `Film`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT per la tabella `Proiezione`
--
ALTER TABLE `Proiezione`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT per la tabella `Utente`
--
ALTER TABLE `Utente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `Biglietto`
--
ALTER TABLE `Biglietto`
  ADD CONSTRAINT `Biglietto_ibfk_1` FOREIGN KEY (`utente`) REFERENCES `Utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Biglietto_ibfk_2` FOREIGN KEY (`proiezione`) REFERENCES `Proiezione` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `Film`
--
ALTER TABLE `Film`
  ADD CONSTRAINT `Film_ibfk_1` FOREIGN KEY (`candidatore`) REFERENCES `Utente` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `Proiezione`
--
ALTER TABLE `Proiezione`
  ADD CONSTRAINT `Proiezione_ibfk_1` FOREIGN KEY (`film`) REFERENCES `Film` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `_Like`
--
ALTER TABLE `_Like`
  ADD CONSTRAINT `_Like_ibfk_1` FOREIGN KEY (`utente`) REFERENCES `Utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `_Like_ibfk_2` FOREIGN KEY (`film`) REFERENCES `Film` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
