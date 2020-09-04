<?php namespace application\repositories;


use PDO;

class ConfigurationRepository extends CommonRepository
{
    public static function migration_version(): int {
        return intval(self::get_setting('migration_order'));
    }

    public static function admin_mails(): array {
        return explode(',', self::get_setting('admin_mails'));
    }

    private static function get_setting(string $key): string {
        $query = self::get_connection()->prepare('select * from settings where setting_key = :key');
        $query->bindParam(':key', $key);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['value'];
    }

    public static function get_game_rates(): array {
        $query = "select * from settings where setting_key in ('drop_rate','gold_rate','exp_rate','jp_rate')";
        $stmt = self::get_connection()->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
        return array_map(function($val) { return intval($val[0]); }, $results);
    }
}