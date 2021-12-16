SET
SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET
AUTOCOMMIT = 0;
START TRANSACTION;
SET
time_zone = "+00:00";

drop table if exists Utente;
drop table if exists Film;
drop table if exists _Like;
drop table if exists Proiezione;
drop table if exists Biglietto;

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

create table Film
(
    id          int             not null auto_increment primary key,
    nome        varchar(20)     not null,
    descrizione text            not null,
    durata      int             not null,
    anno        smallint        not null,
    regista     varchar(50)     not null,
    produttore  varchar(50)     not null,
    cast        text            not null,
    in_gara     tinyint         not null,
    approvato   tinyint         not null,
    candidatore int,
    foreign key (candidatore) references Utente (id) on update cascade on delete set null
);

create table _Like
(
    utente int not null,
    film   int not null,
    primary key (utente, film),
    foreign key (utente) references Utente (id) on update cascade on delete cascade,
    foreign key (film) references Film (id) on update cascade on delete cascade
);

create table Proiezione
(
    id     int      not null auto_increment primary key,
    orario datetime not null,
    film   int      not null,
    foreign key (film) references Film (id) on update cascade on delete cascade
);

create table Biglietto
(
    id         int not null auto_increment primary key,
    utente     int not null,
    proiezione int not null,
    foreign key (utente) references Utente (id) on update cascade on delete cascade,
    foreign key (proiezione) references Proiezione (id) on update cascade on delete cascade
);

commit;