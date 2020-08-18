<?php
/** @noinspection PhpUnused */

namespace application\controllers;


use application\services\ChestService;

class Chest_Controller
{

    public static function check_chest_state() {
        return ChestService::check_chest($_GET['chest']);
    }

    public static function open() {
        return ChestService::loot_chest($_POST['chest'], $_POST['game_id']);
    }

    public static function fill() {
        return ChestService::fill_chest($_POST['chest'], intval($_POST['item_type']), intval($_POST['item_id']), $_POST['game_id']);
    }

    public static function feedback() {
        return ChestService::check_feedback($_POST['game_id'], $_POST['token'], intval($_POST['type']), intval($_POST['value']));
    }
}