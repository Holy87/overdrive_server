<?php


namespace application\repositories;


use application\models\UserPreferences;
use PDO;

class UserPreferencesRepository extends CommonRepository
{
    public static function create_preferences(int $user_id): bool {
        $query = self::get_connection()->prepare('insert into user_preferences (user_id) value (:id)');
        $query->bindParam(':id', $user_id);
        return $query->execute();
    }

    public static function get_user_preferences(int $user_id): UserPreferences {
        $query = self::get_connection()->prepare('select * from user_preferences where user_id = :id');
        $query->bindParam(':id', $user_id);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return new UserPreferences($result);
    }

    public static function save_preferences(UserPreferences $preferences): bool {
        $query_str = 'update user_preferences set auction_notifications = :auction, reply_notifications = :reply, system_notifications = :syst where user_id = :user';
        $query = self::get_connection()->prepare($query_str);
        $auction = $preferences->get_auction_contact();
        $reply = $preferences->get_reply_contact();
        $system = $preferences->get_system_contact();
        $user_id = $preferences->get_prop('user_id');
        $query->bindParam(':auction', $auction);
        $query->bindParam(':reply', $reply);
        $query->bindParam(':syst', $system);
        $query->bindParam(':user', $user_id);
        return $query->execute();
    }
}