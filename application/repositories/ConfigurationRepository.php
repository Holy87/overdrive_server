<?php namespace application\repositories;


use PDO;

class ConfigurationRepository
{
    public static function migration_version(): int {
        return intval(self::get_setting('migration_order'));
    }

    public static function admin_mails(): array {
        return explode(',', self::get_setting('admin_mails'));
    }

    private static function get_setting(string $key): string {
        $query = get_connection()->prepare('select * from settings where setting_key = :key');
        $query->bindParam(':key', $key);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['value'];
    }
}