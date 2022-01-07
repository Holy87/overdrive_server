<?php


namespace application\repositories;


use application\models\GiftCode;
use PDO;

class GiftCodeRepository extends CommonRepository
{
    public static function get_code(string $code): ?GiftCode {
        $code = self::safe_string($code);
        $query = self::get_connection()->prepare('select * from gift_codes where gift_code = :code');
        $query->bindParam(':code', $code);
        $query->execute();
        return  $query->rowCount() > 0 ? new GiftCode($query->fetch(PDO::FETCH_ASSOC)) : null;
    }

    public static function add_used_code(int $player_id, string $code): bool {
        $code = self::safe_string($code);
        $query = self::get_connection()->prepare('insert into used_codes (gift_code, player_id) VALUES (:code, :player)');
        $query->bindParam(':code', $code);
        $query->bindParam(':player', $player_id);
        return $query->execute();
    }

    public static function delete_code(GiftCode $giftCode): bool {
        $query = self::get_connection()->prepare('delete from gift_codes where gift_code = :code');
        $code = $giftCode->get_code();
        $query->bindParam(':code', $code);
        return $query->execute();
    }

    public static function used_codes(int $player_id): array {
        $player_id = intval($player_id);
        $query = self::get_connection()->prepare('select gift_code from used_codes where player_id = :id');
        $query->bindParam(':id', $player_id);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public static function used_codes_with_rewards(int $player_id): array {
        $player_id = intval($player_id);
        $query = self::get_connection()->prepare('select gift_code from used_codes where player_id = :id');
        $query->bindParam(':id', $player_id);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $codes = [];
        foreach($results as $result) {
            array_push($codes, ['code' => $result['gift_code'], 'rewards' => $result['rewards']]);
        }
        return $codes;
    }
}