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

    public static function max_auctioned_items(): int {
        return intval(self::get_setting('max_auctioned_items'));
    }

    private static function get_setting(string $key): string {
        $query = self::get_connection()->prepare('select * from settings where setting_key = :key');
        $query->bindParam(':key', $key);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['value'];
    }

    public static function get_game_rates(): array {
        $query = "select * from settings where setting_key in ('drop_rate','gold_rate','exp_rate','ap_rate')";
        $stmt = self::get_connection()->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
        return array_map(function($val) { return intval($val[0]); }, $results);
    }

    public static function get_fame_increase_rate(): int {
        return intval(self::get_setting('fame_increase_rate'));
    }

    public static function get_infame_increase_rate(): int {
        return intval(self::get_setting('infame_increase_rate'));
    }

    /**
     * crea un token url per un'azione da intraprendere. Generalmente
     * questi token vengono generati per essere inseriti nelle email per
     * azioni rapide con click.
     * @param string $action
     * @param string|null $params
     * @return string|null
     */
    public static function create_action_url(string $action, ?string $params): ?string {
        $query = "insert into url_tokens (token, action, params) values (:token, :action, :params)";
        $token = generateRandomString(100);
        $encoded_token = password_encode($token);
        if (!isset($params)) $params = '';
        $stmt = self::get_connection()->prepare($query);
        $stmt->bindParam(':token', $encoded_token);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':params', $params);
        if ($stmt->execute()) {
            return $token;
        } else {
            return null;
        }
    }

    public static function get_action_url(string $token) {
        $token_encoded = password_encode($token);
        $query = "select * from url_tokens where token = :token";
        $stmt = self::get_connection()->prepare($query);
        $stmt->bindParam(':token', $token_encoded);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}