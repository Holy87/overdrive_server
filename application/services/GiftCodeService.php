<?php


namespace application\services;


use application\Database;
use application\models\GiftCode;
use application\models\Player;
use application\repositories\GiftCodeRepository;
use Exception;
use services\PlayerService;

class GiftCodeService
{
    public static function get_code_state(?Player $player, ?GiftCode $giftCode): int {
        if ($player == null) return GiftCode::NOT_EXIST;
        if ($giftCode == null) return GiftCode::NOT_EXIST;
        if ($giftCode->get_player_id() != null && $giftCode->get_player_id() != $player->get_id()) return GiftCode::NOT_EXIST;
        if ($giftCode->is_expired()) return GiftCode::EXPIRED;
        $used_codes = GiftCodeRepository::used_codes($player->get_id());
        if (in_array($giftCode->get_code(), $used_codes)) return GiftCode::USED;
        return GiftCode::AVAILABLE;
    }

    public static function get_code_rewards(int $player_id, string $game_token, string $code): ?GiftCode {
        $player = PlayerService::authenticate_player($player_id, $game_token);
        $giftCode = GiftCodeRepository::get_code($code);
        if ( self::get_code_state($player, $giftCode) == GiftCode::AVAILABLE) {
            return $giftCode;
        } else {
            return null;
        }
    }

    public static function use_code(int $player_id, string $game_token, string $code) {
        Database::get_connection()->beginTransaction();
        $player = PlayerService::authenticate_player($player_id, $game_token);
        if ($player->is_banned()) {
            Database::get_connection()->rollBack();
            return operation_failed(banned());
        }
        $giftCode = GiftCodeRepository::get_code($code);
        $state = self::get_code_state($player, $giftCode);
        try {
            if ($state != GiftCode::AVAILABLE) {
                Database::get_connection()->rollBack();
                return operation_failed($state);
            }
            if (!$giftCode->for_all_players()) {
                GiftCodeRepository::delete_code($giftCode);
            }
            GiftCodeRepository::add_used_code($player->get_id(), $giftCode->get_code());
        } catch (Exception $exception) {
            Database::get_connection()->rollBack();
            return operation_failed(SERVER_ERROR, ['message' => $exception->getMessage()]);
        }
        Database::get_connection()->commit();
        return operation_ok($giftCode);
    }

    public static function used_codes(int $player_id, string $game_token): array {
        $player = PlayerService::authenticate_player($player_id, $game_token);
        if ($player == null) return [];
        return GiftCodeRepository::used_codes($player->get_id());
    }

    public static function obtained_rewards(int $player_id, string $game_token): array {
        $player = PlayerService::authenticate_player($player_id, $game_token);
        if ($player == null) return [];
        return GiftCodeRepository::used_codes_with_rewards($player->get_id());
    }


}