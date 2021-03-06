<?php namespace application\controllers;

use application\services\AuctionService;

class Auction_Controller {

    /**
     * Ottiene la lista degli oggetti messi all'asta. L'informazione è pubblica. Se si passa come parametro
     * player_id allora mostra tutti gli oggetti in vendita escluso il giocatore.
     */
    public static function list() {
        return AuctionService::get_auction(isset($_GET['player_id']) ? $_GET['player_id'] : 0);
    }

    public static function sell() {
        return AuctionService::sell_item($_POST['item_type'], $_POST['item_id'], $_POST['quantity'], $_POST['price']);
    }

    /**
     * Compra l'oggetto all'asta. L'operazione è transazionale. Necessita di autenticazione e dell'ID dell'asta
     */
    public static function buy() {
        return AuctionService::buy_item(intval($_POST['auction_id']));
    }

    public static function listed() {
        return AuctionService::auctioned_items();
    }

    public static function sold() {
        return AuctionService::sold_items();
    }

}