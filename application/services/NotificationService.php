<?php


namespace application\services;


use application\Database;
use application\repositories\NotificationRepository;
use application\repositories\PlayerRepository;

class NotificationService
{
    public static function get_and_set_read(string $game_id) {
        $player = PlayerRepository::get_player_from_game($game_id);
        if ($player) {
            Database::get_connection()->beginTransaction();
            $notifications = NotificationRepository::get_unread_notifications($player->get_id());
            NotificationRepository::set_all_read($player->get_id());
            Database::get_connection()->commit();
            return $notifications;
        }
        return [];
    }

    public static function set_all_read(string $game_id) {
        $player = PlayerRepository::get_player_from_game($game_id);
        if ($player) NotificationRepository::set_all_read($player->get_id());
        return ok_response();
    }

    public static function get_unread(string $game_id): array {
        $player = PlayerRepository::get_player_from_game($game_id);
        if ($player) {
            return NotificationRepository::get_unread_notifications($player->get_id());
        } else {
            return [];
        }
    }
}