<?php


namespace application\repositories;


use PDO;

class TitlesRepository extends CommonRepository
{
    public static function get_titles(int $player_id): array {
        $query = self::get_connection()->prepare('select title_id from player_titles where player_id = :id');
        $query->bindParam(':id', $player_id);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * @param int $player_id
     * @param int $title_id
     * @return bool
     */
    public static function unlock_title(int $player_id, int $title_id): bool {
        $query = self::get_connection()->prepare('insert into player_titles (title_id, player_id) VALUES (:title, :player)');
        $query->bindParam(':title', $title_id);
        $query->bindParam(':player', $player_id);
        return $query->execute();
    }
}