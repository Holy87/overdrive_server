<?php


namespace application\models;


class GiftCode extends Entity
{
    public array $serializable = ['gift_code','rewards', 'due_date'];
    public const AVAILABLE = 0;
    public const NOT_EXIST = 100;
    public const USED = 102;
    public const EXPIRED = 103;

    public function for_all_players(): bool {
        return $this->get_prop('use_type') == 0;
    }

    public function is_expired(): bool {
        if ($this->get_prop('due_date') == null) return false;
        return strtotime($this->get_prop('due_date')) < strtotime("Today");
    }

    public function get_player_id(): int {
        return $this->get_prop('player_id');
    }

    public function get_code(): string {
        return $this->get_prop('gift_code');
    }
}