<?php namespace application\models;


class Chest extends Entity
{
    public array $serializable = ['chest_id','chest_name','item', 'owner', 'token'];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->set_prop('item', new RPG_Item($this->get_prop('item_id'), $this->get_prop('item_type')));
        $this->set_prop('owner', new Player($data));
    }

    public function is_full(): bool {
        return $this->get_item()->getItemType() > 0;
    }

    public function get_item(): RPG_Item {
        return $this->get_prop('item');
    }

    public function get_owner(): Player {
        return $this->get_prop('owner');
    }

    public function get_name(): string {
        return $this->get_prop('chest_name');
    }

    public function get_token(): string {
        return $this->get_prop('token');
    }
}