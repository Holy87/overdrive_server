<?php


namespace application;


use PDO;
use PDOException;

class Database
{
    private static PDO $connection;


    /**
     * Ottiene la connessione al database.
     * @return PDO
     */
    public static function get_connection(): PDO {
        if (!isset(self::$connection)) {
            self::$connection = self::new_connection();
        }
        return self::$connection;
    }

    public static function safe_string(string $string): string
    {
        return $string;
    }

    private static function new_connection(): PDO {
        $host = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
        $user = DB_USER;
        $pass = DB_PASS;

        try {
            $link = new PDO($host, $user, $pass);
            $link->setAttribute(PDO::ATTR_EMULATE_PREPARES ,false);
        } catch (PDOException $e) {
            http_response_code(500);
            die('Errore di connessione: '.$e->getMessage());
        }
        return $link;
    }
}