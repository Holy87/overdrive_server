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

    public static function get_board_messages(string $board_id): array {
        return BoardRepository::get_board_messages($board_id);
    }

    public static function post_board_message(string $board_id, string $message) {
        $player = PlayerService::get_logged_player();
        $reply_to = preg_match(self::REPLY_PATTERN, base64_decode($message), $matches) ? $matches[1] : null;
        if (BoardRepository::create_message($player->get_id(), $board_id, $message, $reply_to)) {
            if($reply_to != null) {
                $replied = PlayerRepository::get_player_from_name($reply_to);
                if ($replied) {
                    $player_name = $player->get_name();
                    $player_face = $player->get_face();
                    $info = "$board_id,$player_name,$player_face";
                    NotificationRepository::add_notification($replied->get_id(), Notification::BOARD_REPLY_TYPE, $info);
                }
            }
            return operation_ok();
        }
        return operation_failed(unprocessed());
    }
}