<?php


namespace application\models;


class Notification extends Entity
{
    public array $serializable = ['notification_id', 'type', 'additional_info', 'is_read', 'date'];
    public const GET_FAME_TYPE = 0;
    public const GET_INFAME_TYPE = 1;
    public const BANNED_TYPE = 2;
    public const BOARD_REPLY_TYPE = 3;
    public const AUCTION_SELL_TYPE = 4;

    public function is_read(): bool {
        return $this->get_prop('is_read') > 0;
    }
}