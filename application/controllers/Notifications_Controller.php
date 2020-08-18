<?php
/** @noinspection PhpUnused */

namespace application\controllers;
use application\repositories\NotificationRepository;


class Notifications_Controller {
    public static function read(string $game_id) {
        $notifications = NotificationRepository::get_unread_notifications($game_id);
        NotificationRepository::set_all_read($game_id);
        return json_encode($notifications);
    }
}