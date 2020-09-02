<?php namespace application\repositories;

use application\models\Player;
use PDO;

class PlayerRepository extends CommonRepository {

    public static function get_player_from_id(int $player_id): ?Player {
        return self::get_player_from('player_id', intval($player_id));
    }

    /**
     * da usare solo nel caso di creazioe del giocatore.
     * @param string $game_token
     * @return Player|null
     */
    public static function get_player_from_game(string $game_token): ?Player {
        $game_token = password_encode($game_token);
        return self::get_player_from('game_token', $game_token);
    }

    public static function get_player_from_name(string $name): ?Player {
        return self::get_player_from('player_name', self::safe_string($name));
    }

    public static function get_player_from(string $column, $key): ?Player {
        $query = self::get_connection()->prepare("select * from players where $column = :key");
        $query->bindParam(':key', $key);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result && $query->rowCount() > 0) {
            return new Player($result);
        } else {
            return null;
        }
    }

    public static function save_player(Player $player): bool {
        $query_str = 'UPDATE players SET player_face = :face, points = :points, level = :level, story = :story, quests = :quests, fame = :fame, infame = :infame, hours = :hours, minutes = :minutes where player_id = :id';
        $query = self::get_connection()->prepare($query_str);
        $face = intval($player->get_face());
        $points = intval($player->get_points());
        $level = intval($player->get_level());
        $story = intval($player->get_story());
        $quests = intval($player->get_quests());
        $hours = intval($player->get_playtime()['hours']);
        $minutes = intval($player->get_playtime()['minutes']);
        $fame = intval($player->get_fame());
        $infame = intval($player->get_infame());
        $id = intval($player->get_id());

        $query->bindParam(':face', $face);
        $query->bindParam(':points', $points);
        $query->bindParam(':level', $level);
        $query->bindParam(':story', $story);
        $query->bindParam(':quests', $quests);
        $query->bindParam(':hours', $hours);
        $query->bindParam(':minutes', $minutes);
        $query->bindParam(':fame', $fame);
        $query->bindParam(':infame', $infame);
        $query->bindParam(':id', $id);

        return $query->execute();
    }

    public static function create_player(string $game_token, string $name, $face): bool {
        $game_token = password_encode($game_token);
        $name = self::safe_string($name);
        $face = intval($face);
        $query_str = 'INSERT INTO players (game_token, player_name, player_face) VALUES (:game_token, :name, :face)';
        $query = self::get_connection()->prepare($query_str);

        $query->bindParam(':game_token', $game_token);
        $query->bindParam(':name', $name);
        $query->bindParam(':face', $face);

        return $query->execute();
    }

    public static function check_name_exist(string $name): bool {
        $name = self::safe_string($name);
        $query = self::get_connection()->prepare('SELECT * FROM players where player_name = :name');
        $query->bindParam(':name', $name);
        $query->execute();
        return $query->rowCount() > 0;
    }

    public static function check_game_token_exist(string $game_token): bool {
        $game_token = self::safe_string($game_token);
        $query = self::get_connection()->prepare('SELECT * FROM players where game_token = :game_token');
        $query->bindParam(':game_token', $game_token);
        $query->execute();
        return $query->rowCount() > 0;
    }

}