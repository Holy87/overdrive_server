<?php namespace  application\repositories;
use application\models\Player;
use application\models\RPG_Item;
use PDO;
use PDOStatement;
use application\models\AuctionItem;

class AuctionRepository extends CommonRepository {
    private const SELECT = 'select * from auction_items i join players p on p.player_id = i.seller_id';

    private const WHERE = ' where ';



    public static function get_items(int $player_id = 0): array {
        $select = self::SELECT.self::WHERE.'i.customer_id is null and p.banned = 0 and p.player_id <> :id';
        $query = self::get_connection()->prepare($select);
        $query->bindParam(':id', $player_id);
        return self::get_results($query);
    }

    public static function add_auction_item(Player $seller, RPG_Item $item, int $quantity, string $token, int $price): bool {
        $query = 'insert into auction_items (seller_id, item_type, item_id, item_num, price, token) VALUES (:seller_id, :type, :item_id, :num, :price, :token)';
        $stmt = self::get_connection()->prepare($query);
        $seller_id = $seller->get_id();
        $item_type = $item->getItemType();
        $item_id = $item->getId();
        $stmt->bindParam(':seller_id', $seller_id);
        $stmt->bindParam(':type', $item_type);
        $stmt->bindParam(':item_id', $item_id);
        $stmt->bindParam(':num', $quantity);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':price', $price);
        return $stmt->execute();
    }

    public static function get_sold_items(int $player_id): array {
        $select = self::SELECT.self::WHERE.'i.customer_id is not null and p.player_id = :id';
        $query = self::get_connection()->prepare($select);
        $query->bindParam(':id', $player_id);
        return self::get_results($query);
    }

    public static function get_auctioned_items(int $player_id): array {
        $select = self::SELECT.self::WHERE.'i.customer_id is null and p.player_id = :id';
        $query = self::get_connection()->prepare($select);
        $query->bindParam(':id', $player_id);
        return self::get_results($query);
    }

    public static function get_results(PDOStatement $query): array {
        $query->execute();
        if ($query) {
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function($data) { return new AuctionItem($data); }, $results);
        } else {
            return [];
        } 
    }

    public static function get_item(int $auction_id): ?AuctionItem {
        $query = self::get_connection()->prepare(self::SELECT.self::WHERE.'i.auction_id = :id');
        $query->bindParam(':id', $auction_id);
        $query->execute();
        if ($query->rowCount() > 0) {
            return new AuctionItem($query->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }
 
    public static function remove_item(int $player_id, int $auction_id): bool {
        $delete = 'delete from auction_items where seller_id = :player and auction_id = :auction and customer_id is null';
        $query = self::get_connection()->prepare($delete);
        $query->bindParam(':player', $player_id);
        $query->bindParam(':auction', $auction_id);
        if ($query->execute()) {
            return $query->rowCount() > 0;
        } else {
            return false;
        }
    }

    public static function buy_item(int $customer_id, int $auction_id): bool {
        $update = 'update auction_items set customer_id = :customer where auction_id = :auction and customer_id is null';
        $query = self::get_connection()->prepare($update);
        $query->bindParam(':customer', $customer_id);
        $query->bindParam(':auction', $auction_id);
        if ($query->execute()) {
            return $query->rowCount() > 0;
        } else {
            return false;
        }
    }

}