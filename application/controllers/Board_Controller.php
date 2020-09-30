<?php /** @noinspection PhpUnused */


namespace application\controllers;


use application\services\BoardService;

class Board_Controller
{
    /**
     * Ottiene messaggi da una sfera dimensionale con codice board_id.
     * @return array
     */
    public static function get_messages() {
        return BoardService::get_board_messages($_GET['board_id']);
    }

    /**
     * Aggiunge un messaggio ad una sfera dimensionale.
     * @return array
     */
    public static function post_message() {
        return BoardService::post_board_message($_POST['player_id'], $_POST['game_token'], $_POST['board_id'], $_POST['message']);
    }
}