<?php namespace application\services;

use application\Database;
use application\models\FeedbackToken;
use application\models\Notification;
use application\repositories\ChestRepository;
use application\repositories\NotificationRepository;
use application\repositories\PlayerRepository;
use application\repositories\TokenRepository;
use services\PlayerService;
use Exception;

class ChestService {
    // codici di risposta
    public const CHEST_EMPTY = 100;
    public const CHEST_FULL = 101;
    public const FILLED = 105;
    public const NOT_FILLED = 106;
    public const PLAYER_SAME = 107;
    public const TOKEN_ERROR = 108;

    // fama e infamia
    public const FAME_FEED_TYPE = 0;
    public const INFAME_FEED_TYPE = 1;
    public const FAME_INCREASE_RATE = 1; // quanta fama aumenta per ogni ringraziamento
    public const INFAME_INCREASE_RATE = 3; // quanta infamia aumenta per ogni mostro che sconfigge gli eroi

    public static function check_chest(string $chest_name): int {
        return ChestRepository::get_chest($chest_name) == null ? self::CHEST_EMPTY : self::CHEST_FULL;
    }

    public static function fill_chest(string $chest_name, int $item_type, int $item_id) {
        $player = PlayerService::get_logged_player();
        if (self::check_chest($chest_name) == self::CHEST_FULL) operation_failed(self::CHEST_FULL);
        Database::get_connection()->beginTransaction();
        $token = TokenRepository::create_token($player->get_id(), FeedbackToken::CHEST_TYPE);
        if ($token == null) {
            Database::get_connection()->rollBack();
            return operation_failed(self::TOKEN_ERROR);
        }
        if (ChestRepository::create_chest($chest_name, $item_type, $item_id, $player->get_id(), $token->get_code())) {
            Database::get_connection()->commit();
            return operation_ok(self::FILLED);
        } else {
            Database::get_connection()->rollBack();
            return operation_failed(self::NOT_FILLED);
        }
    }

    public static function loot_chest(string $chest_name) {
        $player = PlayerService::get_logged_player();
        Database::get_connection()->beginTransaction();
        try {
            $chest = ChestRepository::get_chest($chest_name);
            if ($chest == null)  {
                Database::get_connection()->rollBack();
                return operation_failed(self::NOT_FILLED);
            }
            if ($chest->get_owner()->get_id() == $player->get_id()) {
                Database::get_connection()->rollBack();
                return operation_failed(self::PLAYER_SAME);
            }
            ChestRepository::delete_chest($chest_name);
            Database::get_connection()->commit();
            return operation_ok($chest);
        } catch (Exception $exception) {
            Database::get_connection()->rollBack();
            return operation_failed(self::NOT_FILLED);
        }
    }

    public static function check_feedback(string $token_code, int $type) {
        $token = TokenRepository::find_token($token_code);
        if ($token == null) return operation_failed(self::TOKEN_ERROR);
        $player = PlayerRepository::get_player_from_id($token->get_player_id());
        if ($player == null) return operation_failed(self::TOKEN_ERROR);
        $sender = PlayerService::get_logged_player();
        if ($sender == null) return operation_failed(player_unregistered());
        if ($type == self::FAME_FEED_TYPE) {
            $player->add_fame(self::FAME_INCREASE_RATE);
            NotificationRepository::add_notification($player->get_id(), Notification::GET_FAME_TYPE, $sender->get_name());
        } else {
            $player->add_infame(self::INFAME_INCREASE_RATE);
            NotificationRepository::add_notification($player->get_id(), Notification::GET_INFAME_TYPE, $sender->get_name());
        }
        PlayerRepository::save_player($player);
        TokenRepository::delete_token($token->get_id());
        return operation_ok();
    }
}