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
values (1, "utente", "utente", 2000 - 01 - 01, "utente@utente.com", "utente123", 0),
       (2, "admin", "admin", 2000 - 01 - 01, "admin@admin.com", "admin123", 1);

create table Film
(
    id          int         not null auto_increment primary key,
    nome        varchar(20) not null,
    descrizione text        not null,
    durata      int         not null,
    anno        smallint    not null,
    regista     varchar(50) not null,
    produttore  varchar(50) not null,
    cast        text        not null,
    in_gara     tinyint     not null,
    approvato   tinyint     not null,
    candidatore int,
    foreign key (candidatore) references Utente (id) on update cascade on delete set null
);

INSERT INTO `Film` (`nome`, `descrizione`, `durata`, `anno`, `regista`, `produttore`, `cast`, `in_gara`, `approvato`, `candidatore`) VALUES
('Gilberto Filé - L\'addio', 'Due grandi amici del celebre professore Unipd celebrano il suo ritiro ricordando vari momenti classici della vita dell\'egregio professor Filé.\r\nTra gossip e storie piccanti, il percorso didattico del professore storico di Informatica Unipd viene descritto con zelo e precisione senza precedenti.', 120, 2020, 'Alessandro Sperduti, Caterina Sartori', 'Unipd', 'Vari membri dell\'università di Padova', 1, 1, 2),
('Il Padrino', 'Il film è ambientato a New York in pieno dopoguerra, tra la fine degli anni 1940 e la prima metà degli anni 1950. Il protagonista è don Vito Corleone, capo di una famiglia mafiosa divenuta col tempo una delle più potenti organizzazioni criminali della Grande Mela, grazie al rispetto e all\'onorabilità ottenute dal patriarca e dai figli coinvolti nelle attività malavitose. Quando don Vito rimane vittima di un attentato da parte di un boss rivale, il figlio Michael Corleone comincia l\'ascesa nell\'impero criminale della famiglia, fino a diventare il nuovo \"padrino\".', 175, 1972, 'Francis Ford Coppola', 'Albert S. Ruddy', 'Marlon Brando, Al Pacino, James Caan', 0, 1, NULL),
("film1", "desc", 120, 1999, "regista paolo", "prod paolo", "paolo; paolo2", 1, 1, 1);

create table _Like
(
    utente int not null,
    film   int not null,
    primary key (utente, film),
    foreign key (utente) references Utente (id) on update cascade on delete cascade,
    foreign key (film) references Film (id) on update cascade on delete cascade
);

INSERT INTO _Like (utente, film) VALUES
(1, 1),
(1, 2),
(2, 1);

create table Proiezione
(
    id     int      not null auto_increment primary key,
    orario datetime not null,
    film   int      not null,
    foreign key (film) references Film (id) on update cascade on delete cascade
);

insert into Proiezione(orario, film) values
('2021-09-18 18:00:00', 1),
('2021-09-18 21:00:00', 2),
('2021-09-18 23:00:00', 3),
('2021-09-19 19:00:00', 3),
('2021-09-19 22:00:00', 1),
('2021-09-19 23:30:00', 2);

create table Biglietto
(
    id         int not null auto_increment primary key,
    utente     int not null,
    proiezione int not null,
    foreign key (utente) references Utente (id) on update cascade on delete cascade,
    foreign key (proiezione) references Proiezione (id) on update cascade on delete cascade
);

insert into Biglietto(id, utente, proiezione)
values (1, 1, 1);

commit;