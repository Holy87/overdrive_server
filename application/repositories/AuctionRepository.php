<?php namespace  application\repositories;
use PDO;
use PDOStatement;
use application\models\AuctionItem;

class AuctionRepository extends CommonRepository {
    private const SELECT = 'select i.auction_id, as auction_id, i.item_type as item_type, i.item_id as item_id,
    i.item_number as item_number, i.price as price, p.player_name as player_name, p.player_face as player_face,
    p.points as points, p.level as level, p.banned as banned, p.story as story, p.fame as fame, p.infame as infame from 
    auction_items i join players p on i.player_id = p.player_id where ';



    public static function get_items(int $player_id = 0): array {
        $select = self::SELECT.'i.customer_id is null and p.banned = 0 and p.player_id <> :id';
        $query = self::get_connection()->prepare($select);
        $query->bindParam(':id', $player_id);
        return self::get_results($query);
    }

    public static function get_sold_items(int $player_id): array {
        $select = self::SELECT.'i.customer_id is not null and p.player_id = :id';
        $query = self::get_connection()->prepare($select);
        $query->bindParam(':id', $player_id);
        return self::get_results($query);
    }

    public static function get_auctioned_items(int $player_id): array {
        $select = self::SELECT.'i.customer_id is null and p.player_id = :id';
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

    public static function get_item(int $auction_id): AuctionItem {
        $query = self::get_connection()->prepare(self::SELECT.'i.auction_id = :id');
        $query->bindParam(':id', $auction_id);
        $query->execute();
        if ($query->rowCount() > 0) {
            return new AuctionItem($query->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }
 
    public static function remove_item(int $player_id, int $auction_id): bool {
        $delete = 'delete from auction_items where player_id = :player and auction_id = :auction and customer_id is null';
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