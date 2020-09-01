<?php


namespace application\services;
use application\models\BoardMessage;
use application\models\Notification;
use application\repositories\BoardRepository;
use application\repositories\NotificationRepository;
use application\repositories\PlayerRepository;


class BoardService
{
    const REPLY_PATTERN = '/@(\w+)/';

    public static function get_board_messages(string $sphere_id): array {
        $results = BoardRepository::get_joined_messages($sphere_id);
        $messages = [];
        foreach ($results as $result) {
            $message = new BoardMessage($result);
            array_push($messages, $message);
        }
        return $messages;
    }

    public static function post_board_message(string $game_id, string $sphere_id, string $message) {
        $player = PlayerRepository::get_player_from_game($game_id);
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