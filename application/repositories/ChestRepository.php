<?php


namespace application\repositories;


use application\models\Chest;

class ChestRepository
{
    public static function get_chest(string $chest_name): ?Chest {
        $query = get_connection()->prepare('select * from chests where chest_name = :name');
        $chest_name = safe_string($chest_name);
        $query->bindParam(':name', $chest_name);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result && $query->rowCount() > 0) {
            return new Chest($result);
        } else {
            return null;
        }
    }

    public static function create_chest(string $chest_name, int $item_type, int $item_id, string $game_id, string $token): bool {
    $query = get_connection()->prepare('insert into chests (chest_name, item_type, item_id, game_id, token) VALUES (:name, :type, :item, :game, :token)');
    $query->bindParam(':name', $chest_name);
    $query->bindParam(':type', $item_type);
    $query->bindParam(':item', $item_id);
    $query->bindParam(':game', $game_id);
    $query->bindParam(':token', $token);
    return $query->execute();
    }

    public static function delete_chest(string $chest_name): bool {
        $query = get_connection()->prepare('delete from chests where chest_name = :name');
        $chest_name = safe_string($chest_name);
        $query->bindParam(':name', $chest_name);
        return $query->execute();
    }


}