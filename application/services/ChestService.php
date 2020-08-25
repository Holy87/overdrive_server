<?php namespace application\services;

use application\Database;
use application\models\FeedbackToken;
use application\models\Notification;
use application\models\RPG_Item;
use application\repositories\ChestRepository;
use application\repositories\NotificationRepository;
use application\repositories\PlayerRepository;
use application\repositories\TokenRepository;

class ChestService {
    public const CHEST_FULL = 'full';
    public const CHEST_EMPTY = 'empty';
    public const NOT_FILLED = 1;
    public const FILLED = 'ok';
    public const PLAYER_SAME = 'same';
    public const TOKEN_ERROR = 'token';
    public const FAME_FEED_TYPE = 0;
    public const INFAME_FEED_TYPE = 1;

    public static function check_chest(string $chest_name): string {
        return ChestRepository::get_chest($chest_name) == null ? self::CHEST_EMPTY : self::CHEST_FULL;
    }

    public static function fill_chest(string $chest_name, int $item_type, int $item_id, string $game_id) {
        $player = PlayerRepository::get_player_from_game($game_id);
        if ($player == null) return player_unregistered();
        if ($player->is_banned()) return banned();
        if (self::check_chest($chest_name) == self::CHEST_FULL) self::NOT_FILLED;
        Database::get_connection()->beginTransaction();
        $token = TokenRepository::create_token($player->get_id(), FeedbackToken::CHEST_TYPE);
        if ($token == null) return self::NOT_FILLED;
        if (ChestRepository::create_chest($chest_name, $item_type, $item_id, $game_id, $token->get_code())) return self::FILLED;
        return self::NOT_FILLED;
    }

    public static function loot_chest(string $chest_name, string $game_id) {
        $player = PlayerRepository::get_player_from_game($game_id);
        if ($player == null) return player_unregistered();
        if ($player->is_banned()) return banned();

        $chest = ChestRepository::get_chest($chest_name);
        if ($chest == null) return self::NOT_FILLED;
        if ($chest->get_owner_id() == $game_id) return self::PLAYER_SAME;
        $owner = PlayerRepository::get_player_from_game($chest->get_owner_id());
        $item = $chest->get_item();
        $item->setOwnerId($owner->get_game_id());
        $item->setOwnerName($owner->get_name());
        ChestRepository::delete_chest($chest_name);

        return json_encode($item);
    }

    public static function check_feedback(string $game_id, string $token_code, int $type, int $value) {
        $token = TokenRepository::find_token($token_code);
        if ($token == null) return self::TOKEN_ERROR;
        $player = PlayerRepository::get_player_from_id($token->get_player_id());
        if ($player == null) return self::TOKEN_ERROR;
        $sender = PlayerRepository::get_player_from_game($game_id);
        if ($sender == null) return player_unregistered();
        if ($type == self::FAME_FEED_TYPE) {
            $player->add_fame($value);
            NotificationRepository::add_notification($player->get_game_id(), Notification::GET_FAME_TYPE, $sender->get_name());
        } else {
            $player->add_infame($value);
            NotificationRepository::add_notification($player->get_infame(), Notification::GET_INFAME_TYPE, $sender->get_name());
        }
        PlayerRepository::save_player($player);
        TokenRepository::delete_token($token->get_id());
        return ok_response();
    }
}