<?php


namespace application\services;


use application\Database;
use application\repositories\NotificationRepository;
use services\PlayerService;

class NotificationService
{
    public static function get_and_set_read() {
        $player = PlayerService::get_logged_player();
        if ($player) {
            Database::get_connection()->beginTransaction();
            $notifications = NotificationRepository::get_unread_notifications($player->get_id());
            NotificationRepository::set_all_read($player->get_id());
            Database::get_connection()->commit();
            return $notifications;
        }
        return [];
    }

    public static function set_all_read() {
        $player = PlayerService::get_logged_player();
        if ($player) NotificationRepository::set_all_read($player->get_id());
        return operation_ok();
    }

    public static function get_unread(): array {
        $player = PlayerService::get_logged_player();
        if ($player) {
            return NotificationRepository::get_unread_notifications($player->get_id());
        } else {
            return [];
        }
    }
}