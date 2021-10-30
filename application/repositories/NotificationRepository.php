<?php


namespace application\repositories;


use application\models\Notification;
use PDO;

class NotificationRepository extends CommonRepository
{
    public static function get_unread_notifications(int $player_id): array {
        $query = self::get_connection()->prepare('select * from player_notifications where is_read = 0 and player_id = :id');
        $query->bindParam(':id', $player_id);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($data) { return new Notification($data); }, $results);
    }

    public static function set_all_read(int $player_id): bool {
        $query = self::get_connection()->prepare('update player_notifications set is_read = 1 where player_id = :id');
        $query->bindParam(':id', $player_id);
        return $query->execute();
    }

    /**
     * @param int $player_id
     * @param int $type
     * @param string|null $additional_info
     * @return bool
     */
    public static function create_notification(int $player_id, int $type = 0, string $additional_info = null): bool {
        $query = self::get_connection()->prepare('insert into player_notifications (player_id, type, additional_info) values (:game, :type, :info)');
        $query->bindParam(':game', $player_id);
        $query->bindParam(':type', $type);
        $query->bindParam(':info', $additional_info);
        return $query->execute();
    }

    public static function delete_old_notifications() {
        $query = 'delete from player_notifications where is_read = 1 and date < date_sub(current_date, interval 2 month)';
        $stmt = self::get_connection()->prepare($query);
        return $stmt->execute();
    }

    public static function is_duplicate(int $player_id, int $type, string $info = null): bool {
        $query = self::get_connection()->prepare('select * from player_notifications where player_id = :player_id and type = :type and additional_info = :info');
        $query->bindParam(':player_id', $player_id);
        $query->bindParam(':type', $type);
        $query->bindParam(':info', $info);
        $query->execute();
        return $query->rowCount() > 0;
    }
}