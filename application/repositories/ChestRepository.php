<?php


namespace application\repositories;


use application\models\Chest;
use PDO;

class ChestRepository extends CommonRepository
{
    public static function get_chest(string $chest_name): ?Chest {
        $query = self::get_connection()->prepare('select c.player_id as player_id, * from chests c join players p on p.player_id = c.player_id where chest_name = :name');
        $chest_name = self::safe_string($chest_name);
        $query->bindParam(':name', $chest_name);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result && $query->rowCount() > 0) {
            return new Chest($result);
        } else {
            return null;
        }
    }

    public static function create_chest(string $chest_name, int $item_type, int $item_id, int $player_id, string $token): bool {
    $query = self::get_connection()->prepare('insert into chests (chest_name, item_type, item_id, player_id, token) VALUES (:name, :type, :item, :game, :token)');
    $query->bindParam(':name', $chest_name);
    $query->bindParam(':type', $item_type);
    $query->bindParam(':item', $item_id);
    $query->bindParam(':game', $player_id);
    $query->bindParam(':token', $token);
    return $query->execute();
    }

    public static function delete_chest(string $chest_name): bool {
        $query = self::get_connection()->prepare('delete from chests where chest_name = :name');
        $chest_name = self::safe_string($chest_name);
        $query->bindParam(':name', $chest_name);
        return $query->execute();
    }


}