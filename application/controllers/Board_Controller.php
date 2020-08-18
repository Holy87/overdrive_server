<?php /** @noinspection PhpUnused */


namespace application\controllers;


use application\services\BoardService;

class Board_Controller
{
    public static function get_messages() {
        return json_encode(BoardService::get_board_messages($_GET['sphere_id']));
    }

    public static function post_message() {
        return BoardService::post_board_message($_POST['game_id'], $_POST['sphere_id'], $_POST['message'], $_POST['reply']);
    }
}