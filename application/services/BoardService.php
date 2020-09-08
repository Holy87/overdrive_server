<?php


namespace application\services;
use application\models\Notification;
use application\repositories\BoardRepository;
use application\repositories\NotificationRepository;
use application\repositories\PlayerRepository;
use services\PlayerService;


class BoardService
{
    const REPLY_PATTERN = '/@(\w+)/';

    public static function get_board_messages(string $sphere_id): array {
        return BoardRepository::get_joined_messages($sphere_id);
    }

    public static function post_board_message(int $player_id, string $game_token, string $sphere_id, string $message) {
        $player = PlayerService::authenticate_player($player_id, $game_token);
        $reply_to = preg_match(self::REPLY_PATTERN, $message, $matches) ? $matches[1] : null;
        if ($player == null) return player_unregistered();
        if ($player->is_banned()) return banned();
        if (BoardRepository::create_message($player->get_id(), $sphere_id, $message, $reply_to)) {
            if($reply_to != null) {
                $replied = PlayerRepository::get_player_from_name($reply_to);
                if ($replied) {
                    $player_name = $player->get_name();
                    $player_face = $player->get_face();
                    $info = "$sphere_id,$player_name,$player_face";
                    NotificationRepository::add_notification($replied->get_id(), Notification::BOARD_REPLY_TYPE, $info);
                }
            }
            return ok_response();
        }
        return unprocessed();
    }
}