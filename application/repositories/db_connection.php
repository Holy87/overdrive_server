<?php



function get_connection(): PDO {
    $host = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;

    try {
        $link = new PDO($host, $user, $pass);
        $link->setAttribute(PDO::ATTR_EMULATE_PREPARES ,false);
    } catch (PDOException $e) {
        die('Errore di connessione: '.$e->getMessage());
    }
    return $link;
}

function safe_string($string)
{
    return $string;
}