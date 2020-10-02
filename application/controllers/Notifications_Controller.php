<?php
/** @noinspection PhpUnused */

namespace application\controllers;
use application\services\NotificationService;


class Notifications_Controller {
    public static function read() {
        return NotificationService::get_and_set_read();
    }

    public static function unreads() {
        return NotificationService::get_unread();
    }

    public static function set_all_read() {
        return NotificationService::set_all_read();
    }

    public static function unread_count() {
        return sizeof(NotificationService::get_unread());
    }
}