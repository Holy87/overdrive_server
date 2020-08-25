<?php


namespace application\repositories;
use application\Database;
use PDO;


class CommonRepository
{
    public static function get_connection(): PDO {
        return Database::get_connection();
    }

    public static function safe_string(string $str): string {
        return Database::safe_string($str);
    }
}