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
    private string $owner_name;
    private string $owner_id; // player id
    private string $token; // token di certificazione oggetto

    function __construct($id, $item_type)
    {
        $this->id = $id;
        $this->item_type = $item_type;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getItemType(): int
    {
        return $this->item_type;
    }

    public function getOwnerId(): string
    {
        return $this->owner_id;
    }

    public function getOwnerName(): string
    {
        return $this->owner_name;
    }

    public function setOwnerId(string $owner_id)
    {
        $this->owner_id = $owner_id;
    }

    public function setOwnerName(string $owner_name)
    {
        $this->owner_name = $owner_name;
    }

    public function setToken(string $token) {
        $this->token = $token;
    }

    public function jsonSerialize()
    {
        return json_encode([
            'player_name' => $this->getOwnerName(),
            'player_id' => $this->getOwnerId(),
            'item_type' => $this->getItemType(),
            'item_id' => $this->getId(),
            'token' => $this->token
        ]);
    }
}