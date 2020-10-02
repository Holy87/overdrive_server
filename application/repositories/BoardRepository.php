<?php namespace application\repositories;

use application\models\BoardMessage;
use application\models\Player;
use PDO;

class BoardRepository extends CommonRepository
{
    // restituisce il resultset come join tra messaggio ed autore
    private const BASE_QUERY = 'select *, players.player_id as player_id from board_messages
left join players on players.player_id = board_messages.player_id';
    private const WHERE = ' where ';

    // restituisce un solo messaggio con ID specifico
    public static function get_message(int $message_id): BoardMessage {
        $message_id = intval($message_id);
        $query = self::get_connection()->prepare(self::BASE_QUERY.self::WHERE.'message_id = :id');
        $query->bindParam(':id', $message_id);
        $query->execute();
        return new BoardMessage($query->fetch(PDO::FETCH_ASSOC));
    }

    // restituisce tutti i messaggi di una board
    public static function get_board_messages(string $board_id): array {
        $query_str = self::BASE_QUERY.self::WHERE.'board_id = :board';
        $board_id = self::safe_string($board_id);
        $query = self::get_connection()->prepare($query_str);
        $query->bindParam(':board', $board_id);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($data) { return new BoardMessage($data); }, $results);
    }

    public static function create_message(int $player_id, string $board_id, string $message, string $reply_to = null) {
        $query = self::get_connection()->prepare('insert into board_messages (player_id, board_id, message, reply_to) values (:player, :board, :message, :reply)');
        $query->bindParam(':player', $player_id);
        $query->bindParam(':board', $board_id);
        $query->bindParam(':message', $message);
        $query->bindParam(':reply', $reply_to);
        return $query->execute();
    }

    /**
     * prende i vecchi messaggi di Overdrive Cap. 3 e li converte al nuovo giocatore registrato
     * questa operazione viene effettuata quando un nuovo giocatore si registra
     * @param string $game_token
     * @param Player $new_player
     * @return int
     */
    public static function assign_legacy_messages(string $game_token, Player $new_player): int {
        $player_id = $new_player->get_id();
        $update = 'update board_messages set player_id = :id, old_player_name = null, legacy_game_id = null where legacy_game_id = :game';
        $query = self::get_connection()->prepare($update);
        $query->bindParam(':id', $player_id);
        $query->bindParam(':game', $game_token);
        $query->execute();
        return $query->rowCount();
    }
}