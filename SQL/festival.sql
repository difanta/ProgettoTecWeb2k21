SET
SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET
AUTOCOMMIT = 0;
START TRANSACTION;
SET
time_zone = "+00:00";

drop table if exists _Like;
drop table if exists Biglietto;
drop table if exists Proiezione;
drop table if exists Film;
drop table if exists Utente;

create table Utente
(
    id              int         not null auto_increment primary key,
    nome            varchar(20) not null,
    cognome         varchar(20) not null,
    data_di_nascita date        not null,
    email           varchar(40) not null unique,
    password        varchar(40) not null,
    admin           tinyint     not null
);

insert into Utente(id, nome, cognome, data_di_nascita, email, password, admin)
values (1, "utente", "utente", "2000-01-01" , "user", "user", 0),
       (2, "admin", "admin", "2000-01-01" , "admin", "admin", 1),
	   (3, "alberto", "tecweb", "2000-02-02" , "alberto@unipd.it", "alberto123", 1),
	   (4, "tommaso", "tecweb", "2000-04-07" , "tommaso@unipd.it", "tommaso123", 1),
	   (5, "luca", "tecweb", "2000-07-04" , "luca@unipd.it", "luca123", 1),
	   (6, "giovanni", "tecweb", "2000-09-06", "giovanni@unipd.it", "giovanni123", 1),
	   (7, "mario", "draghi", "1964-02-02" , "ildragone@.palazzochigi.it", "mario123", 0),
	   (8, "giorgia", "meloni", "1973-04-07" , "lagiorgia@fdi.it", "giorgia123", 0),
	   (9, "sergio", "mattarella", "1955-07-04", "sergiomattarella@quirinale.it", "sergio123", 0),
	   (10, "virginia", "raggi", "1979-09-06" , "virgy79@comunediroma.it", "virginia123", 0),
	   (11, "ombretta", "gaggi", "1989-07-04" , "ombretta@unipd.it", "ombretta123", 0),
	   (12, "daniela", "mapelli", "1982-09-06" , "dmap@unipd.it", "daniela123", 0),
	   (13, "jacques", "demy", "1945-12-03" , "jacquesdemy@pariscinema.fr", "jacques123", 0),
	   (14, "agostino", "ferrente", "1992-11-11" , "ferri@gmail.com", "agostino123", 0);

create table Film
(
    id          int         not null auto_increment primary key,
    nome        varchar(50) not null unique,
    descrizione text        not null,
    durata      int         not null,
    anno        smallint    not null,
    regista     varchar(50) not null,
    produttore  varchar(50) not null,
    cast        text        not null,
    in_gara     tinyint     not null,
    approvato   tinyint     not null,
    candidatore int,
    alt         varchar(125),
    foreign key (candidatore) references Utente (id) on update cascade on delete set null
);

INSERT INTO `Film` (`nome`, `descrizione`, `durata`, `anno`, `regista`, `produttore`, `cast`, `in_gara`, `approvato`, `candidatore`,`alt`) VALUES
("Gilberto Filé - L'addio", "Due grandi amici del celebre professore Unipd celebrano il suo ritiro ricordando vari momenti classici della vita dell'egregio professor Filé. Tra gossip e storie piccanti, il percorso didattico del professore storico di Informatica Unipd viene descritto con zelo e precisione senza precedenti.", 120, 2020, "Francesco Ranzato, Alessandro Sperduti", "Unipd", "Vari membri dell'università di Padova", 1, 1, 11,"Filé assieme ad altri professori mentre presiede una proclamazione di laurea in Informatica."),
("Il Padrino", "La mafia è una cosa talmente brutta quanto bello è questo film. La storia di un uomo mafioso a capo di losche operazioni si intreccia alle vicissitudini di una coppia innamorata. Riusciranno Anna e Blanco a fuggire dalla criminalità e vivere una vita normale?", 175, 1972, "Francis Ford Coppola", "Albert S. Ruddy", "Marlon Brando, Al Pacino, James Caan", 0, 1, NULL,"Lo sguardo potente di Don Vito Corleone"),
("La favolosa storia di pelle d'asino", "Un adattamento di una classica favola francese. Catherine Deneuve è una principessa costretta a stratagemmi per evitare di sposare il proprio padre.", 90, 1970, "Jacques Demy", "paris cinema", "Catherine Deneuve; Jean Marais; Jacques Perrin", 1, 0, 13,""),
("Selfie", "Gli adolescenti Giorgio e Gianfranchio sono due amici napoletani che hanno deciso di riprendersi con un iPhone, commentando quotidianamente la propria vita e raccontando una tragedia comune.", 78, 2019, "Agostino Ferrente", "Berdugo productions", "Alessandro Antonelli; Pietro Orlando", 1, 0, 14,""),
("Les parapluies de Cherbourg", "Les parapluies de Cherbourg racconta della giovane Geneviève (Catherine Deneuve) e della storia d'amore con Guy, meccanico chiamato alle armi per la guerra d’Algeria.", 92, 1964, "Catherine Deneuve", "paris cinema", "Catherine Deneuve; Nino Castelnuovo; Anne Vernon", 1, 0, 10,""),
("Involuntary", "Cinque episodi di tragicomica quotidianità intrecciati che mettono a fuoco i condizionamenti sociali a cui è sottoposto l’individuo.", 98, 2008, "Ruben Ostlund", "Chigi films", "Maria Lundqvist; Leif Edlund; Vera Vitali", 1, 1, 7,"Una maestra ed un'alunna guardano un'illusione ottica di due linee apparentemente uguali."),
("First Cow", "Intorno al 1820, un cuoco in viaggio verso l’Oregon e un immigrato cinese in cerca di fortuna tentano rubare il latte della preziosa mucca Jersey del proprietario terriero.", 135, 2019, "Kelly Reichardt", "mubi productions", "John Magaro; Orion Lee", 1, 1, 12,"Una mucca viene trasportata su una zattera su un fiume."),
("Anne at 13,000 ft", "Anne, una ragazza dalla vita apparentemente normale, viene sommersa dalla pressione della quotidianità dopo aver fatto paracadutismo per l’addio al nubilato della sua migliore amica. Conosce Matt e inizia ad andare oltre ciò che è socialmente accettabile.", 75, 2019, "Kazir Radwanski", "Sergio Mattarella", "Deragh Campbell; Matt Johnson", 1, 0, 9,""),
("Zombi Child", "Haiti, 1962. Un uomo viene fatto resuscitare solo per lavorare nelle piantagioni di canna da zucchero. 55 anni dopo a Parigi, una ragazza haitiana confessa l'antico segreto di famiglia a delle amiche, provocando conseguenze irreparabili.", 103, 2019, "Bertrand Bonello", "Sergio Mattarella", "Louise Labeque; Wislanda Louimat", 1, 0, 9,""),
("Friends and strangers", "Ray e Alice decidono di andare in campeggio dopo un incontro casuale. Una serie di goffi approcci romantici causano un’imbarazzante tensione tra i due. Di ritorno a Sydney, Ray cerca di convincere uno strano cliente ad affidargli un incarico.", 84, 2021, "James Vaughan", "Sergio Mattarella", "Emma Diaz; Greg Zimbulis", 1, 0, 9,""),
("Racconto d'inverno", "Félicie vive con la madre e lavora come parrucchiera. Ha una figlia avuta da Charles, un giovane con il quale ha vissuto una storia d' amore durante una vacanza in Bretagna. Per un lapsus, però, i due non riusciranno a tenersi in contatto. Cinque anni dopo Félicie non ha ancora dimenticato Charles.", 109, 1992, "Eric Rohmer", "users productions", "Charlotte Very; Frederic Van Den Driessche", 1, 1, 1,"Una coppia si scambia uno sguardo d'amore mentre abbracciano una bambina."),
("Un coteau dans le coeur", "Parigi, 1979, Anne è un'attrice porno, per riconquistare la fiducia del suo compagno decide di finanziare un film molto più ambizioso quando un assassino uccide uno degli attori. Anne continuerà le riprese senza sapere se il killer agirà di nuovo.", 102, 2018, "Yann Gonzalez", "users production", "Vanessa Paradis; Kate Moran; Nicolas Maury", 1, 0, 1,""),
("Shiva Baby", "Danielle affronta degli incontri imbarazzanti durante una shiva, una riunione funebre ebraica. Tra parenti dispotici, è irritata dalla ricomparsa di un’ex fidanzata e del suo sugar daddy segreto.", 77, 2020, "Emma Seligman", "mubi productions", "Rachel Sennott", 1, 1, 12,"Una ragazza con sguardo infastidito mentre tiene uno stuzzichino."),
("Le notti di Cabiria", "Una simpatica prostituta romana vive molte delusioni, ma forse riesce a trovare un brav'uomo che finalmente la ama.", 110, 1957, "Federico Fellini", "De Laurentiis", "Giulietta Masina; Francois Perier", 0, 1, null,"Cabiria, vestita con una pelliccia, sorride in una strada romana."),
("Nuovo cinema Paradiso", "La vita di Salvatore, un regista di successo, è partita da una piccola cittadina italiana, dove il cinema ha emozionato e incantato. Il proiezionista stringe amicizia con il piccolo Salvatore e tra burle e fatiche l'amore per il cinema entra nel suo cuore.", 173, 1988, "Giuseppe Tornatore", "Cristaldifilms", "Salvatore Cascio; Philippe Noiret", 0, 1, null,"Un bambino guarda affascinato la pellicola di un film nella sala di proiezione."),
("Midsommar", "Degli amici partecipano ad una folkloristica festa in Svezia. Con entusiasmo si avvicinano ai loro costumi, accettando anche i più buffi. Quando le cose si fanno spaventose e assurde, Dani decide di fuggire, o forse di restare...", 148, 2019, "Ari Aster", "B-Reel Films", "Florence Pugh; Jack Reynor; Villhelm Blomgren", 0, 1, null,"Due donne in abiti tipici svedesi stanno bevendo, una è spaventata."),
("La parola ai giurati", "11 uomi bianchi etero e Henry Fonda, che interpreta il dodicesimo uomo bianco etero, decidono della colpevolezza di un piccolo ragazzo afroamericano, accusato di omicidio.", 96, 1957, "Sidney Lumet", "Big productions", "Henry Fonda; Lee J. Cobb", 0, 1, null,"I membri di una giuria, seduti attorno ad un tavolo, guardano spazientiti lo spettatore."),
("Il dottor Stranamore", "La situazione nella guerra fredda si fa scottante quando un pazzo generale americano decide di sganciare le bombe. L'intervento delle alte autorità provano a fermare il disastro sul nascere, ma non riescono a contattare un intrepido pilota pronto a tutto. Un pazzo scienziato tedesco fa un monologo nel finale.", 95, 1964, "Stanley Kubrick", "Columbia Pictures", "Peter Sellers", 0, 1, null,"Un uomo con lo sguardo da matto con sullo sfondo dei generali miltari e una mappa del mondo."),
("The Florida Project", "Moone ed i suoi amici ne combinano di tutti i colori.  Ma la loro vita alle periferie di un famoso parco divertimenti è soggetta al disagio del mondo degli adulti.", 111, 2017, "Sean Baker", "Cre Film", "Brooklynn Prince; Bria Vinaite", 0, 1, null,"Una ragazza e due bambini sono seduti ad un tavolo. Lei fuma, loro mangiano cibo d'asporto."),
("Arrival", "Una linguista ed un fisico hanno il compito di imparare a comunicare con gli alieni, per capire cosa ci fanno sulla terra chiusi in dodici astronavi sparse per il mondo.", 116, 2016, "Denis Villeneuve", "Lava Bear Films", "Amy Adams; Jeremy Renner", 0, 1, null,"Delle persona in tuta isolante si avvicinano ad un varco rettangolare di una grotta.");

create table _Like
(
    utente int not null,
    film   int not null,
    primary key (utente, film),
    foreign key (utente) references Utente (id) on update cascade on delete cascade,
    foreign key (film) references Film (id) on update cascade on delete cascade
);

INSERT INTO _Like (utente, film) VALUES
(1,6),
(7,6),
(9,6),
(12,6),
(13,6),
(14,6),
(3,13),
(4,13),
(5,13),
(6,13),
(10,13),
(1,1),
(3,1),
(4,1),
(5,1),
(7,1),
(8,1),
(9,1),
(12,1),
(11,16),
(12,16),
(13,16),
(7,19),
(8,19),
(9,19),
(10,19),
(3,2),
(1,3),
(7,4),
(4,5),
(9,12),
(1,14),
(1,15),
(1,17),
(4,17),
(1,18);

create table Proiezione
(
    id     int      not null auto_increment primary key,
    orario datetime not null,
    film   int      not null,
    foreign key (film) references Film (id) on update cascade on delete cascade
);

insert into Proiezione(orario, film) values
('2022-09-12 15:00:00', 7),
('2022-09-12 18:30:00', 11),
('2022-09-12 21:30:00', 1),
('2022-09-12 23:45:00', 6),
('2022-09-13 18:00:00', 6),
('2022-09-13 21:00:00', 7),
('2022-09-13 23:30:00', 13),
('2022-09-14 18:00:00', 11),
('2022-09-14 21:00:00', 11),
('2022-09-15 18:00:00', 13),
('2022-09-15 21:00:00', 1),
('2022-09-16 18:00:00', 14),
('2022-09-16 21:00:00', 19),
('2022-09-16 23:30:00', 16),
('2022-09-17 18:00:00', 17),
('2022-09-17 21:00:00', 18),
('2022-09-17 23:15:00', 19),
('2022-09-18 16:00:00', 20),
('2022-09-18 18:30:00', 16),
('2022-09-18 21:30:00', 15),
('2022-09-18 23:00:00', 20);

create table Biglietto
(
    id         int not null auto_increment primary key,
    utente     int not null,
    proiezione int not null,
    foreign key (utente) references Utente (id) on update cascade on delete cascade,
    foreign key (proiezione) references Proiezione (id) on update cascade on delete cascade
);

insert into Biglietto(utente, proiezione)
values (1, 1),
(1,2),
(1,6),
(3,12),
(3,20),
(4,14),
(4,16),
(5,11),
(5,4),
(6,5),
(6,6),
(6,7),
(6,9),
(6,14),
(6,16),
(7,12),
(7,13),
(8,1),
(8,2),
(8,3),
(9,16),
(9,17),
(9,19),
(11,7),
(11,8),
(11,12),
(12,11),
(12,15),
(12,16),
(13,17),
(13,12),
(13,13),
(13,16),
(14,9),
(14,11),
(14,7),
(14,2),
(14,1),
(14,3),
(14,4);

commit;