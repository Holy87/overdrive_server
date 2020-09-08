<?php namespace application\repositories;

use application\models\BoardMessage;
use application\models\Player;
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
from messages m left join players p on m.player_id = p.player_id
where sphere_id = :board';
        $board_id = self::safe_string($board_id);
        $query = self::get_connection()->prepare($query_str);
        $query->bindParam(':board', $board_id);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($data) { return new BoardMessage($data); }, $results);
    }

    public static function create_message(int $player_id, string $sphere_id, string $message, string $reply_to = null) {
        $query = self::get_connection()->prepare('insert into messages (player_id, sphere_id, message, reply_to) values (:player, :sphere, :message, :reply)');
        $query->bindParam(':player', $player_id);
        $query->bindParam(':sphere', $sphere_id);
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
        $update = 'update messages set player_id = :id, player_name = null, legacy_game_id = null where legacy_game_id = :game';
        $query = self::get_connection()->prepare($update);
        $query->bindParam(':id', $player_id);
        $query->bindParam(':game', $game_token);
        $query->execute();
        return $query->rowCount();
    }
}