<?php namespace application\models;

use JsonSerializable;

/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 04/06/2016
 * Time: 14:02
 */
class RPG_Item implements JsonSerializable
{
    private int $id;
    private int $item_type;

    // tipi merce
    public const ITEM_TYPE = 1;
    public const WEAPON_TYPE = 2;
    public const ARMOR_TYPE = 3;

    function __construct(float $id, int $item_type)
    {
        $this->id = $id;
        $this->item_type = $item_type;
    }

    public function getId(): float
    {
        return $this->id;
    }

    public function getItemType(): int
    {
        return $this->item_type;
    }

    public function is_item() {
        return $this->item_type == self::ITEM_TYPE;
    }

    public function jsonSerialize()
    {
        return [
            'item_type' => $this->getItemType(),
            'item_id' => $this->getId()
        ];
    }
}