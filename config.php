<?php
// CONFIGURAZIONE DELL'APPLICAZIONE
namespace Application\Config;

define('APP_NAME', 'Overdrive RPG');
define('APP_URL', 'overdrive');
define('APP_AUTHOR', 'Francesco Bosso');

// Connessione al DB
define('DB_HOST', 'localhost');
define('DB_TYPE', 'mysql');
define('DB_NAME', 'my_overdriverpg');
define('DB_USER', 'root');
define('DB_PASS', '');

// Attiva la codifica automatica di TUTTE le risposte in BASE64
define('AUTO_ENCODE_BASE64', false);

// Impostazioni email

// Indirizzi mittenti per utenti ed amministratori. Il mittente sarà ad esempio info@nomedominio.
define('MAIL_SENDER', 'info');
define('ADMIN_SENDER', 'admin');
define('SENDER_NAME', 'Overdrive RPG');

// Impostazioni applicazione
define('ROOT_PATH', './');
define('MAINTENANCE_MODE', false);

// Messaggi vari
define('MAINTENANCE_MESSAGE', 'Il servizio è in manutenzione');
