<?php namespace services;

use application\Database;
use application\models\Player;
use application\repositories\BoardRepository;
use application\repositories\PlayerRepository;
use Exception;
use utils\WordChecker;

class PlayerService
{
    public const VALID = 0;
    public const NAME_ALREADY_PRESENT = 1;
    public const NAME_WORD_FORBIDDEN = 2;
    public const SPECIAL_CHARACTER_NOT_ALLOWED = 3;
    public const CREATION_ERROR = 5;

    /**
     * Questa funzione dovrebbe essere chiamata nel momento di ogni azione che deve
     * fare il giocatore o di ogni informazione che solo il giocatore interessato
     * dovrebbe avere. Si tratta di autenticare l'ID del giocatore (pubblico) con
     * la sua chiave (privata).
     * @param int $player_id
     * @param string $game_token
     * @return Player|null
     */
    public static function authenticate_player(int $player_id, string $game_token): ?Player {
        $player_id = intval($player_id);
        $game_token = PlayerRepository::safe_string($game_token);
        $player = PlayerRepository::get_player_from_id($player_id);
        if ($player == null) return null;
        if ($player->get_game_token() != password_encode($game_token)) return null;
        return $player;
    }

    public static function name_is_valid(string $name): int {
        if (WordChecker::has_forbidden_word($name)) return self::NAME_WORD_FORBIDDEN;
        if (WordChecker::has_special_characters($name)) return self::SPECIAL_CHARACTER_NOT_ALLOWED;
        if (PlayerRepository::check_name_exist($name)) return self::NAME_ALREADY_PRESENT;
        return self::VALID;
    }

    /**
     * crea il giocatore e aggiusta i messaggi della sfera dimensionale.
     * Restituisce un hash con risultato dell'operazione e ID del nuovo giocatore.
     * @param string $game_token
     * @param string $name
     * @param int $face_id
     * @return array
     */
    public static function create_player(string $game_token, string $name, int $face_id): array {
        if (PlayerRepository::check_game_token_exist($game_token)) ['status'=>false, 'motive'=>self::CREATION_ERROR];
        $name_check = self::name_is_valid($name);
        if ($name_check > 0) ['status'=>false, 'motive'=>$name_check];
        Database::get_connection()->beginTransaction();
        $result = PlayerRepository::create_player($game_token, $name, $face_id);
        if (!$result) {
            Database::get_connection()->rollBack();
            return ['status'=>false, 'motive'=>self::CREATION_ERROR];
        }
        try {
            BoardRepository::assign_legacy_messages($game_token, PlayerRepository::get_player_from_game($game_token));
        } catch (Exception $exception) {
            Database::get_connection()->rollBack();
            internal_server_error($exception->getMessage());
            return ['status'=>false, 'motive'=>self::CREATION_ERROR, 'message'=>$exception->getMessage()];
        }
        Database::get_connection()->commit();
        return ['status'=>true, 'player_id'=>PlayerRepository::get_player_from_game($game_token)->get_id()];
    }
}