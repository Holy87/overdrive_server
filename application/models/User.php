<?php


namespace application\models;


class User extends Entity
{
    public array $serializable = ['user_id', 'user_name', 'mail', 'reg_date', 'banned', 'mail_validated'];

    public function get_mail(): string {
        return $this->get_prop('mail');
    }

    public function get_user_name(): string {
        return $this->get_prop('user_nem');
    }

    public function is_banned(): bool {
        return $this->get_prop('banned');
    }

    public function get_id(): int {
        return $this->get_prop('user_id');
    }
}