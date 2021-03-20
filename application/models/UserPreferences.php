<?php


namespace application\models;

class UserPreferences extends Entity
{
    public array $serializable = ['auction_notifications', 'reply_notifications', 'system_notifications'];

    public function get_auction_contact(): bool {
        return $this->get_prop('auction_notifications');
    }

    public function get_reply_contact(): bool {
        return $this->get_prop('reply_notifications');
    }

    public function get_system_contact(): bool {
        return $this->get_prop('system_notifications');
    }
}