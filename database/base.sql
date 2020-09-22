CREATE TABLE IF NOT EXISTS players
(
    player_id   int(11)      NOT NULL AUTO_INCREMENT,
    player_name varchar(20)  NOT NULL,
    player_face int(11)               DEFAULT NULL,
    title_id    int                   default null,
    points      int(11)               DEFAULT 0,
    reg_date    timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    game_token  varchar(128) NOT NULL comment 'codice di gioco criptato',
    level       int(11)               DEFAULT '0',
    hours       int(4)                DEFAULT '0' COMMENT 'Ore di gioco',
    minutes     int(2)                DEFAULT '0' COMMENT 'Minuti di gioco',
    banned      int(11)               DEFAULT '0' COMMENT '1 se bannato',
    story       int(11)      NOT NULL DEFAULT '0' COMMENT 'Stato della storia',
    quests      int(11)      NOT NULL DEFAULT '0' COMMENT 'Missioni completate',
    fame        int(11)      NOT NULL DEFAULT '0' COMMENT 'fama',
    infame      int(11)      NOT NULL DEFAULT '0' COMMENT 'infamia',
    PRIMARY KEY (player_id),
    UNIQUE KEY user_name (player_name),
    UNIQUE KEY game_token (game_token)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1 COMMENT ='Tabella giocatori'
  AUTO_INCREMENT = 12;

create table users
(
    user_id        int auto_increment,
    user_name      varchar(30)                         not null,
    mail           varchar(100)                        not null,
    password       char(128)                           null comment 'password codificata in sha3-512',
    reg_date       timestamp default CURRENT_TIMESTAMP null,
    banned         int       default 0                 null,
    mail_validated int       default 0                 null comment 'se 1, la mail è stata verificata'
)
    comment 'tabella degli utenti registrati';

create unique index users_mail_uindex
    on users (mail);

create unique index users_user_id_uindex
    on users (user_id);

create unique index users_user_name_uindex
    on users (user_name);

alter table users
    add constraint users_pk
        primary key (user_id);

create table if not exists settings
(
    setting_key varchar(30) unique not null,
    value       varchar(50) default null
);

CREATE TABLE IF NOT EXISTS chests
(
    chest_id   int(11)     NOT NULL AUTO_INCREMENT,
    chest_name varchar(30) DEFAULT NULL COMMENT 'giusto per ricordarmi',
    item_type  int(11)     NOT NULL COMMENT '0: nulla, 1: item, 2: weap. 3: armor',
    item_id    float       DEFAULT NULL,
    player_id  int         NOT NULL,
    token      varchar(20) NOT NULL,
    PRIMARY KEY (chest_id),
    UNIQUE KEY chest_name (chest_name)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 40;

CREATE TABLE IF NOT EXISTS sphere
(
    sphere_id   varchar(20) NOT NULL,
    sphere_name varchar(30) DEFAULT NULL,
    PRIMARY KEY (sphere_id)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS messages
(
    message_id        int(11)     NOT NULL AUTO_INCREMENT,
    legacy_game_token varchar(14)          default NULL comment 'tiene traccia del giocatore non registrato',
    sphere_id         varchar(20) NOT NULL,
    player_id         int         null,
    message           text        NOT NULL,
    date              timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    old_player_name   varchar(10)          DEFAULT NULL COMMENT 'nome di utente non ancora registrato',
    reply_to          varchar(30)          default null comment 'nome del giocatore in risposta',
    PRIMARY KEY (message_id)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 22;

create table player_achievements
(
    player_id      int                                 not null,
    achievement_id int                                 not null,
    unlock_date    timestamp default CURRENT_TIMESTAMP null,
    constraint player_achievements_pk
        primary key (player_id, achievement_id)
)
    comment 'obiettivi sbloccati dai giocatori';

create table player_notifications
(
    notification_id int auto_increment,
    player_id       int                                 not null,
    date            timestamp default CURRENT_TIMESTAMP null,
    type            int       default 0                 null,
    additional_info varchar(50)                         null comment 'altre informazioni per arricchire la notifica',
    is_read         int       default 0                 not null comment 'se ad 1, la notifica è stata letta',
    primary key (notification_id)
)
    comment 'notifiche al giocatore dal server';

CREATE TABLE IF NOT EXISTS feedback_tokens
(
    token_id  int(11)     NOT NULL AUTO_INCREMENT,
    token     varchar(20) NOT NULL,
    player_id int         NOT NULL,
    type      int(11)     NOT NULL DEFAULT 0,
    PRIMARY KEY (token_id)
)
    comment 'token di risposta per gli scrigni - PER NON BARARE'
    ENGINE = MyISAM
    DEFAULT CHARSET = latin1
    AUTO_INCREMENT = 38;

create table gift_codes
(
    gift_code varchar(20)   not null comment 'codice da inserire',
    due_date  date          null comment 'data di scadenza fino a quando si può utilizzare',
    use_type  int default 0 not null comment 'tipo di utilizzo. 0: un solo giocatore, 1: tutti i giocatori',
    rewards   varchar(100)  not null comment 'codici ricompense separati da virgole',
    player_id int           null comment 'ID giocatore (se è per un giocatore specifico)',
    primary key (gift_code)
)
    comment 'codici regalo';

create table used_codes
(
    gift_code varchar(20)  not null comment 'codice usato',
    player_id int          not null comment 'giocatore che ha usato il codice',
    use_date  timestamp    not null default CURRENT_TIMESTAMP,
    rewards   varchar(100) not null comment 'tiene traccia delle ricompense per salvataggi più vecchi',
    primary key (gift_code)
)
    comment 'codici usati dai giocatori';

create table auction_items
(
    auction_id  int   not null auto_increment,
    player_id   int   not null comment 'codice venditore',
    item_type   int   not null comment 'tipo articolo - 0: item, 1: weapon, 2: armor',
    item_id     float not null comment 'ID articolo di gioco',
    item_num    int   not null default 1 comment 'quantità venduta',
    price       int   not null comment 'prezzo di vendita',
    token       int   not null comment 'tiene traccia del salvataggio',
    insert_date timestamp      default CURRENT_TIMESTAMP,
    customer_id int   null comment 'codice del compratore, se comprato',
    primary key (auction_id)
)
    comment 'articoli in vendita per altri giocatori';

create table player_titles
(
    title_id int not null comment 'titolo sbloccato',
    player_id int not null comment 'ID giocatore',
    unlock_date timestamp default current_timestamp,
    primary key (title_id, player_id)
)
    comment 'rappresenta la tabella dei titoli speciali conferiti dal server';

create table events
(
    event_id int not null,
    event_name varchar(50) not null comment 'nome dell''evento',
    switch_id int default 0 null,
    start_date date null comment 'data di inizio dell''evento',
    end_date date null comment 'data di fine dell''evento',
    exp_rate int default 100 not null comment 'rate exp del gioco durante l''evento. 100 = 100% (non cambia)',
    jp_rate int default 100 not null comment 'rate PA del gioco durante l''evento. 100 = 100% (non cambia)',
    gold_rate int default 100 not null comment 'rate drop oro del gioco durante l''evento. 100 = 100% (non cambia)',
    drop_rate int default 100 not null comment 'rate drop del gioco durante l''evento. 100 = 100% (non cambia)'
)
    comment 'eventi del gioco come Natale, Pasqua ecc....';

alter table events
    add constraint events_pk
        primary key (event_id);




insert into settings (setting_key, value)
VALUES ('migration_order', '1');
insert into settings (setting_key, value)
VALUES ('admin_mails', '');
insert into settings (setting_key, value) VALUES ('exp_rate', '100');
insert into settings (setting_key, value) VALUES ('gold_rate', '100');
insert into settings (setting_key, value) VALUES ('drop_rate', '100');
insert into settings (setting_key, value) VALUES ('jp_rate', '100');