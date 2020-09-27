<?php namespace application\services;

use application\models\RPG_Item;
use application\repositories\AuctionRepository;
use application\Database;
use application\models\Notification;
use application\repositories\ConfigurationRepository;
use application\repositories\NotificationRepository;
use Exception;
use services\PlayerService;

class AuctionService {
    public const OK = 0;
    public const MAX_AUCITONED_ERROR = -5;
    public const GENERIC_AUCTION_ERROR = -15;
    public const QUANTITY_ERROR = -16;
    public const ITEM_NOT_FOUND = -17;

    public static function get_auction(int $player_id = 0): array {
        return AuctionRepository::get_items($player_id);
    }

    public static function sell_item(int $player_id, string $game_token, float $item_id, int $item_type, int $quantity, int $price): array {
        $player = PlayerService::authenticate_player($player_id, $game_token);
        if ($player == null) return operation_failed(player_unregistered());
        if ($player->is_banned()) return operation_failed(banned());
        if (sizeof(self::auctioned_items($player_id, $game_token)) >= ConfigurationRepository::max_auctioned_items())
            return operation_failed(self::MAX_AUCITONED_ERROR);
        $item = new RPG_Item($item_id, $item_type);
        if (!$item->is_item() && $quantity != 1) return operation_failed(self::QUANTITY_ERROR);
        if ($quantity > 99) return operation_failed(self::QUANTITY_ERROR);
        if ($quantity < 1) return operation_failed(self::QUANTITY_ERROR);
        $token = generateRandomString(20);
        $result = AuctionRepository::add_auction_item($player, $item, $quantity, $token, $price);
        return $result ? operation_ok($token) : operation_failed(self::GENERIC_AUCTION_ERROR);
    }

    /*
    Imposta l'oggetto in asta come acquistato ed aggiunge la notifica
    al venditore.
    */
    public static function buy_item(int $player_id, string $game_token, int $auction_id): array {
        $player = PlayerService::authenticate_player($player_id, $game_token);
        if ($player == null) return operation_failed(player_unregistered());
        if ($player->is_banned()) return operation_failed(banned());
        Database::get_connection()->beginTransaction();
        try {
            $result = AuctionRepository::buy_item($player->get_id(), $auction_id);
            if (!$result) {
                Database::get_connection()->rollBack();
                return operation_failed(self::ITEM_NOT_FOUND);
            }
            $auctionItem = AuctionRepository::get_item($auction_id);
            $seller = $auctionItem->get_player();
            $info = $auctionItem->get_item()->getId().','.$auctionItem->get_item()->getItemType().','.$auctionItem->get_price();
            NotificationRepository::add_notification($seller->get_id(), Notification::AUCTION_SELL_TYPE, $info);
            Database::get_connection()->commit();
            return operation_ok();
        } catch (Exception $e) {
            Database::get_connection()->rollBack();
            return operation_failed(self::GENERIC_AUCTION_ERROR, ['error' => $e->getMessage()]);
        }
    }

    public static function auctioned_items(int $player_id, string $game_token) {
        $player = PlayerService::authenticate_player($player_id, $game_token);
        if ($player == null) return player_unregistered();
        return AuctionRepository::get_auctioned_items($player->get_id());
    }

    public static function sold_items(int $player_id, string $game_token) {
        $player = PlayerService::authenticate_player($player_id, $game_token);
        if ($player == null) return player_unregistered();
        return AuctionRepository::get_sold_items($player->get_id());
    }
}