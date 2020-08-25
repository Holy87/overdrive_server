<?php namespace application\repositories;

use application\models\BoardMessage;
use PDO;

class BoardRepository extends CommonRepository
{

    public static function get_message(int $message_id): BoardMessage {
        $message_id = intval($message_id);
        $query = self::get_connection()->prepare('select * from messages where message_id = :id');
        $query->bindParam(':id', $message_id);
        $query->execute();
        return new BoardMessage($query->fetch(PDO::FETCH_ASSOC));
    }

    // restituisce il resultset come join tra messaggio ed autore
    public static function get_joined_messages(string $board_id): array {
        $query_str = 'select m.message_id as message_id, m.message as message, m.reply_to as reply_to, m.date as date, m.player_name as old_name, p.player_name as player_name, p.level as player_level, p.banned as banned, p.player_face as player_face
from messages m left join players p on m.game_id = p.game_id
where sphere_id = :board';
        $board_id = self::safe_string($board_id);
        $query = self::get_connection()->prepare($query_str);
        $query->bindParam(':board', $board_id);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create_message(string $game_id, string $sphere_id, string $message, string $reply_to = null) {
        $query = self::get_connection()->prepare('insert into messages (game_id, sphere_id, message, reply_to) values (:game, :sphere, :message, :reply)');
        $query->bindParam(':game', $game_id);
        $query->bindParam(':sphere', $sphere_id);
        $query->bindParam(':message', $message);
        $query->bindParam(':reply', $reply_to);
        return $query->execute();
    }
}