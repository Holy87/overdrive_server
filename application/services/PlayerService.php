<?php namespace services;

use application\Database;
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
    public const GAME_ID_ALREADY_TAKEN = 4;
    public const CREATION_ERROR = 5;

    public static function name_is_valid(string $name): int {
        if (WordChecker::has_forbidden_word($name)) return self::NAME_WORD_FORBIDDEN;
        if (WordChecker::has_special_characters($name)) return self::SPECIAL_CHARACTER_NOT_ALLOWED;
        if (PlayerRepository::check_name_exist($name)) return self::NAME_ALREADY_PRESENT;
        return self::VALID;
    }

    public static function create_player(string $game_id, string $name, int $face_id): int {
        if (PlayerRepository::check_game_id_exist($game_id)) return self::GAME_ID_ALREADY_TAKEN;
        $name_check = self::name_is_valid($name);
        if ($name_check > 0) return $name_check;
        Database::get_connection()->beginTransaction();
        $result = PlayerRepository::create_player($game_id, $name, $face_id);
        if (!$result) {
            Database::get_connection()->rollBack();
            return self::CREATION_ERROR;
        }
        try {
            BoardRepository::assign_legacy_messages($game_id, PlayerRepository::get_player_from_game($game_id));
        } catch (Exception $exception) {
            Database::get_connection()->rollBack();
            return internal_server_error($exception->getMessage());
        }
        Database::get_connection()->commit();
        return self::VALID;
    }

    public static function assign_fame(string $game_id, int $value, string $token, int $type): int {
        $player = PlayerRepository::get_player_from_game($game_id);
        if ($player == null) return player_unregistered();
        if ($player->is_banned()) return banned();

    }

}