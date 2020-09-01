<?php /** @noinspection PhpUnused */

namespace application\controllers;

use application\repositories\AchievementRepository;
use \application\repositories\PlayerRepository;
use services\PlayerService;

class Player_Controller {

    public static function index() {
        if (isset($_GET['player_id'])) {
            return PlayerRepository::get_player_from_id($_GET["player_id"]);
        } elseif (isset($_GET['game_id'])) {
            return PlayerRepository::get_player_from_game($_GET['game_id']);
        } else {
            return player_unregistered();
        }
    }

    public static function update() {
        $player = PlayerRepository::get_player_from_game($_POST['game_id']);
        if ($player) {
            $player->merge($_POST);
            PlayerRepository::save_player($player);
            return ok_response();
        } else {
            return player_unregistered();
        }
    }

    public static function create() {
        return PlayerService::create_player($_POST['game_id'], $_POST['name'], $_POST['face_id']);
    }

    public static function update_face() {
        $player = PlayerRepository::get_player_from_game($_POST['game_id']);
        if ($player) {
            $player->set_face(intval($_POST['face_id']));
            PlayerRepository::save_player($player);
            return http_response_code(200);
        } else {
            return player_unregistered();
        }
    }

    public static function check_name_valid() {
        return PlayerService::name_is_valid($_GET['name']);
    }

    public static function unlock_achievement() {
        $player = PlayerRepository::get_player_from_game($_POST['game_id']);
        if ($player == null) return player_unregistered();
        if ($player->is_banned()) return banned();
        $result = AchievementRepository::unlock_achievement($player->get_id(), intval($_GET['achievement_id']));
        return $result ? ok_response() : unprocessed();
    }

    public static function get_achievements() {
        if (isset($_GET['game_id']))
            $player = PlayerRepository::get_player_from_game($_GET['game_id']);
        else
            $player = PlayerRepository::get_player_from_name($_GET['name']);
        if ($player == null) return player_unregistered();
        return AchievementRepository::get_player_achievements($player->get_id());
    }
}