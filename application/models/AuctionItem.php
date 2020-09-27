<?php namespace application\models;

class AuctionItem extends Entity {
    public array $serializable = ['seller', 'item', 'price', 'item_num', 'auction_id', 'token'];

    public function __construct(array $data)
    {
        $player = new Player($data);
        $item = new RPG_Item($data['item_id'], $data['item_type']);
        $data['seller'] = $player;
        $data['item'] = $item;
        parent::__construct($data);
    }

    public function get_player(): Player {
        return $this->get_prop('player');
    }

    public function get_item(): RPG_Item {
        return $this->get_prop('item');
    }

    public function get_price(): int {
        return $this->get_prop('price');
    }

    public function get_number(): int {
        return $this->get_prop('item_num');
    }
}