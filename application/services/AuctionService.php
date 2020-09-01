<?php namespace application\services;

use application\repositories\AuctionRepository;
use application\repositories\PlayerRepository;
use application\Database;
use application\models\Notification;
use application\repositories\NotificationRepository;
use Exception;

class AuctionService {
    public static function get_auction(string $game_id): array {
        $player = PlayerRepository::get_player_from_game($game_id);
        if ($player == null) return player_unregistered();
        return AuctionRepository::get_items($player->get_id());
    }

    /*
    Imposta l'oggetto in asta come acquistato ed aggiunge la notifica
    al venditore.
    */
    public static function buy_item(string $game_id, int $auction_id): string {
        $player = PlayerRepository::get_player_from_game($game_id);
        if ($player == null) return player_unregistered();
        if ($player->is_banned()) return banned();
        Database::get_connection()->beginTransaction();
        try {
            $result = AuctionRepository::buy_item($player->get_id(), $auction_id);
            if (!$result) {
                Database::get_connection()->rollBack();
                return internal_server_error('Item not found');
            }
            $auctionItem = AuctionRepository::get_item($auction_id);
            $seller = $auctionItem->get_player();
            $info = $auctionItem->get_item()->getId().','.$auctionItem->get_item()->getItemType().','.$auctionItem->get_price();
            NotificationRepository::add_notification($seller->get_id(), Notification::AUCTION_SELL_TYPE, $info);
            Database::get_connection()->commit();
            return ok_response();
        } catch (Exception $e) {
            Database::get_connection()->rollBack();
            return internal_server_error($e->getMessage());
        }
    }

    public static function auctioned_items(string $game_id) {
        $player = PlayerRepository::get_player_from_game($game_id);
        if ($player == null) return player_unregistered();
        return AuctionRepository::get_auctioned_items($player->get_id());
    }

    public static function sold_items(string $game_id) {
        $player = PlayerRepository::get_player_from_game($game_id);
        if ($player == null) return player_unregistered();
        return AuctionRepository::get_sold_items($player->get_id());
    }
}