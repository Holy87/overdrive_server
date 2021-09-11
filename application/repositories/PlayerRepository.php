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

    /**
     * @param int $user_id
     * @return array
     */
    public static function get_players_from_user_id(int $user_id): array {
        $query = self::get_connection()->prepare('select * from players where user_id = :user_id');
        $query->bindParam(':user_id', $user_id);
        $query->execute();
        if ($query) {
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function($data) { return new Player($data); }, $results);
        } else {
            return [];
        }
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
        $query_str = 'UPDATE players SET player_face = :face, exp = :exp, gold = :gold, level = :level, story = :story, quests = :quests, fame = :fame, infame = :infame, hours = :hours, minutes = :minutes, title_id = :title where player_id = :id';
        $query = self::get_connection()->prepare($query_str);
        $face = intval($player->get_face());
        $exp = intval($player->get_exp());
        $gold = intval($player->get_gold());
        $level = intval($player->get_level());
        $story = intval($player->get_story());
        $quests = intval($player->get_quests());
        $hours = intval($player->get_playtime()['hours']);
        $minutes = intval($player->get_playtime()['minutes']);
        $fame = intval($player->get_fame());
        $infame = intval($player->get_infame());
        $id = intval($player->get_id());
        $title = intval($player->get_title());

        $query->bindParam(':face', $face);
        $query->bindParam(':exp', $exp);
        $query->bindParam(':level', $level);
        $query->bindParam(':story', $story);
        $query->bindParam(':quests', $quests);
        $query->bindParam(':hours', $hours);
        $query->bindParam(':minutes', $minutes);
        $query->bindParam(':fame', $fame);
        $query->bindParam(':infame', $infame);
        $query->bindParam(':id', $id);
        $query->bindParam(':gold', $gold);
        $query->bindParam(':title', $title);

        return $query->execute();
    }

    public static function create_player(string $game_token, string $name, int $face, ?int $title): int {
        $game_token = password_encode($game_token);
        $name = self::safe_string($name);
        $face = intval($face);
        $query_str = 'INSERT INTO players (game_token, player_name, player_face, title_id) VALUES (:game_token, :name, :face, :title)';
        $query = self::get_connection()->prepare($query_str);

        $query->bindParam(':game_token', $game_token);
        $query->bindParam(':name', $name);
        $query->bindParam(':face', $face);
        $query->bindParam(':title', $title);

        return $query->execute() ? self::get_connection()->lastInsertId() : 0;
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

    public static function get_party(int $player_id): string {
        if (self::party_exist($player_id)) {
            $query = self::get_connection()->prepare('SELECT * FROM player_party where player_id = :id');
            $query->bindParam(':id', $player_id);
            $query->execute();
            return base64_decode($query->fetch(PDO::FETCH_ASSOC)['party_info']);
        } else {
            return "";
        }
    }

    /**
     * aggiorna, aggiunge o elimina le informazioni sul gruppo
     * @param int $player_id
     * @param ?string $info
     * @return bool
     */
    public static function save_party(int $player_id, ?string $info): bool {
        if (is_null($info) || $info == "") {
            return self::delete_party($player_id);
        }
        if (self::party_exist($player_id)) {
            $query = self::get_connection()->prepare('UPDATE player_party set party_info = :info WHERE player_id = :id');
        } else {
            $query = self::get_connection()->prepare('INSERT INTO player_party (player_id, party_info) VALUES (:id, :info)');
        }
        $query->bindParam(':id', $player_id);
        $query->bindParam(':info', $info);
        return $query->execute();
    }

    public static function party_exist(int $player_id): bool {
        $query = self::get_connection()->prepare('SELECT * FROM player_party where player_id = :id');
        $query->bindParam(':id', $player_id);
        $query->execute();
        return $query->rowCount() > 0;
    }

    public static function delete_party(int $player_id): bool {
        $query = self::get_connection()->prepare('DELETE FROM player_party where player_id = :id');
        $query->bindParam(':id', $player_id);
        return $query->execute();
    }

}