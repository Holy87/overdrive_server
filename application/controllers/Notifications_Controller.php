<?php
/** @noinspection PhpUnused */

namespace application\controllers;
use application\services\NotificationService;


class Notifications_Controller {
    public static function read() {
        return NotificationService::get_and_set_read($_GET['game_id']);
    }

    public static function unreads() {
        return NotificationService::get_unread($_GET['game_id']);
    }

    public static function set_all_read() {
        return NotificationService::set_all_read($_POST['game_id']);
    }
}