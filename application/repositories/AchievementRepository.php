<?php


namespace application\repositories;


use PDO;

class AchievementRepository
{
    public static function get_player_achievements(int $player_id): array {
        $query = get_connection()->prepare('select * from player_achievements where player_id = :id');
        $query->bindParam(':id', $player_id);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_COLUMN, 'achievement_id');
    }

    public static function unlock_achievement(int $player_id, int $achievement_id): bool {
        $query = get_connection()->prepare('insert into player_achievements (player_id, achievement_id) values (:player, :ach)');
        $query->bindParam(':player', $player_id);
        $query->bindParam(':ach', $achievement_id);
        return $query->execute();
    }
}