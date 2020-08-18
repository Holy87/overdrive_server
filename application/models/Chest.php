<?php namespace application\models;


class Chest extends \application\models\Entity
{
    public array $serializable = ['chest_id','chest_name','item_type','item_id','game_id','token'];
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

    public function get_owner_id(): string {
        return $this->get_prop('game_id');
    }

    public function get_name(): string {
        return $this->get_prop('chest_name');
    }

    public function get_token(): string {
        return $this->get_prop('token');
    }
}