<?php namespace application\controllers;

use application\services\AuctionService;

class Auction_Controller {

    public static function list() {
        return AuctionService::get_auction($_GET['game_id']);
    }

    public static function buy() {
        return AuctionService::buy_item($_POST['game_id'], intval($_POST['auction_id']));
    }

    public static function listed() {
        return AuctionService::auctioned_items($_GET['game_id']);
    }

    public static function sold() {
        return AuctionService::sold_items($_GET['game_id']);
    }

}