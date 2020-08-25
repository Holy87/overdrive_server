<?php


namespace application\repositories;


use application\models\Notification;
use PDO;

class NotificationRepository extends CommonRepository
{
    public static function get_unread_notifications(string $game_id): array {
        $query = self::get_connection()->prepare('select * from player_notifications where is_read = 0 and game_id = :id');
        $query->bindParam(':id', $game_id);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $notifications = [];
        foreach ($results as $result) {
            array_push($notifications, new Notification($result));
        }
        return $notifications;
    }

    public static function set_all_read(string $game_id): bool {
        $query = self::get_connection()->prepare('update player_notifications set is_read = 1 where game_id = :id');
        $query->bindParam(':id', $game_id);
        return $query->execute();
    }

    /**
     * @param string $game_id
     * @param int $type
     * @param string|null $additional_info
     * @return bool
     */
    public static function add_notification(string $game_id, int $type = 0, string $additional_info = null): bool {
        $query = self::get_connection()->prepare('insert into player_notifications (game_id, type, additional_info) values (:game, :type, :info)');
        $query->bindParam(':game', $game_id);
        $query->bindParam(':type', $type);
        $query->bindParam(':info', $additional_info);
        return $query->execute();
    }
}