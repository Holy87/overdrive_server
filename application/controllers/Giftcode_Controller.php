<?php


namespace application\controllers;


use application\repositories\GiftCodeRepository;
use application\repositories\PlayerRepository;
use application\services\GiftCodeService;

class Giftcode_Controller
{

    public static function state() {
        $player = PlayerRepository::get_player_from_game($_GET['game_id']);
        $gift_code = GiftCodeRepository::get_code($_GET['code']);
        return GiftCodeService::get_code_state($player, $gift_code);
    }

    public static function rewards() {
        return json_encode(GiftCodeService::get_code_rewards($_GET['game_id'], $_GET['code']));
    }

    public static function use_code() {
        return json_encode(GiftCodeService::use_code($_POST['game_id'], $_POST['code']));
    }

    public static function used_codes() {
        return json_encode(GiftCodeService::used_codes($_GET['game_id']));
    }
}