<?php


namespace application\services;


use application\Database;
use application\repositories\NotificationRepository;
use services\PlayerService;

class NotificationService
{
    public static function get_and_set_read(int $player_id, string $game_token) {
        $player = PlayerService::authenticate_player($player_id, $game_token);
        if ($player) {
            Database::get_connection()->beginTransaction();
            $notifications = NotificationRepository::get_unread_notifications($player->get_id());
            NotificationRepository::set_all_read($player->get_id());
            Database::get_connection()->commit();
            return $notifications;
        }
        return [];
    }

    public static function set_all_read(int $player_id, string $game_token) {
        $player = PlayerService::authenticate_player($player_id, $game_token);
        if ($player) NotificationRepository::set_all_read($player->get_id());
        return ok_response();
    }

    public static function get_unread(int $player_id, string $game_token): array {
        $player = PlayerService::authenticate_player($player_id, $game_token);
        if ($player) {
            return NotificationRepository::get_unread_notifications($player->get_id());
        } else {
            return [];
        }
    }
}