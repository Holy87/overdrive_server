<?php namespace application\models;


class Chest extends Entity
{
    public array $serializable = ['chest_id','chest_name','item_type','item_id','token'];
    private RPG_Item $item;

    public function is_full(): bool {
        return $this->get_prop('item_type') > 0;
    }

    public function get_item(): RPG_Item {
        if ($this->item == null) {
            $this->item = new RPG_Item($this->get_prop('item_id'), $this->get_prop('item_type'));
        }
        return $this->item;
    }

    public function get_owner_id(): int {
        return $this->get_prop('player_id');
    }

    public function get_name(): string {
        return $this->get_prop('chest_name');
    }

    public function get_token(): string {
        return $this->get_prop('token');
    }
}