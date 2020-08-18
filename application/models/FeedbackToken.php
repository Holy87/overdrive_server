<?php


namespace application\models;


class FeedbackToken extends Entity
{
    public array $serializable = ['token', 'player_id', 'type'];
    public const CHEST_TYPE = 0;

    public function get_player_id(): int {
        return $this->get_prop('player_id');
    }

    public function get_code(): string {
        return $this->get_prop('token');
    }

    public function get_type(): int {
        return $this->get_prop('type');
    }

    public function get_id(): int {
        return $this->get_prop('token_id');
    }
}