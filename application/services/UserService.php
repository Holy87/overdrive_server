<?php


namespace application\services;


use application\Database;
use application\models\UserPreferences;
use application\repositories\PlayerRepository;
use application\repositories\UserPreferencesRepository;
use application\repositories\UserRepository;

class UserService
{
    public const USER_NOT_FOUND = 1;
    public const MAIL_ALREADY_PRESENT = 2;
    public const USERNAME_ALREADY_TAKEN = 3;
    public const CREATION_ERROR = 4;
    public const PLAYER_NOT_EXIST = 5;
    public const PLAYER_ALREADY_BOUND = 6;

    public static function register(string $username, string $password, string $mail): array {
        if (UserRepository::find_by_name($username) != null) return operation_failed(self::USERNAME_ALREADY_TAKEN);
        if (UserRepository::find_by_mail($mail) != null) return operation_failed(self::MAIL_ALREADY_PRESENT);
        Database::get_connection()->beginTransaction();
        $new_user_id = UserRepository::create($username, $password, $mail);
        if ($new_user_id == 0 || $new_user_id == null) {
            Database::get_connection()->rollBack();
            return operation_failed(self::CREATION_ERROR);
        }
        if (UserPreferencesRepository::create_preferences($new_user_id)) {
            $_SESSION['user_id'] = $new_user_id;
            Database::get_connection()->commit();
            return operation_ok();
        } else {
            Database::get_connection()->rollBack();
            return operation_failed(SERVER_ERROR);
        }
    }

    public static function login(string $mail_or_name, string $password): array {
        $user = UserRepository::find_with_login($mail_or_name, $password);
        if ($user == null) return operation_failed(self::USER_NOT_FOUND);
        $_SESSION['user_id'] = $user->get_id();
        return operation_ok();
    }

    public static function logout() {
        session_destroy();
    }

    public static function check_mail_valid(string $mail): bool {
        return UserRepository::find_by_mail($mail) != null;
    }

    public static function check_username_valid(string $username): bool {
        return UserRepository::find_by_name($username) != null;
    }

    public static function get_players(int $user_id): array {
        return PlayerRepository::get_players_from_user_id($user_id);
    }

    public static function add_player(int $user_id, int $player_id): array {
        $player = PlayerRepository::get_player_from_id($player_id);
        if ($player_id == null) return operation_failed(self::PLAYER_NOT_EXIST);
        if ($player->get_user_id() != null) return operation_failed(self::PLAYER_ALREADY_BOUND);
        $player->set_user_id($user_id);
        PlayerRepository::save_player($player);
        return operation_ok();
    }

    public static function remove_player(int $player_id): array {
        $player = PlayerRepository::get_player_from_id($player_id);
        if ($player == null) return operation_failed(self::PLAYER_NOT_EXIST);
        $player->set_user_id(null);
        PlayerRepository::save_player($player);
        return operation_ok();
    }

    public static function delete_user(int $user_id): array {
        Database::get_connection()->beginTransaction();
        $user = UserRepository::find_by_id($user_id);
        if ($user == null) {
            Database::get_connection()->rollBack();
            return operation_failed(self::USER_NOT_FOUND);
        }
        UserRepository::delete_user($user_id);
        Database::get_connection()->commit();
        return operation_ok();
    }

    public static function get_preferences(int $user_id): UserPreferences {
        return UserPreferencesRepository::get_user_preferences($user_id);
    }

    public static function save_preference(int $user_id, string $preference_key, $value): array {
        Database::get_connection()->beginTransaction();
        $preferences = self::get_preferences($user_id);
        if ($preferences == null) {
            Database::get_connection()->rollBack();
            return operation_failed(SERVER_ERROR);
        }
        $preferences->set_prop($preference_key, $value);
        UserPreferencesRepository::save_preferences($preferences);
        Database::get_connection()->commit();
        return operation_ok();
    }
}