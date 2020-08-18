<?php


namespace application\models;

class BoardMessage extends Entity
{
    public array $serializable = ['date','message','message_id','old_name','player_name', 'player_level', 'player_face', 'banned', 'reply_to'];

    public function set_author(Player $user) {
        $this->properties['author'] = $user;
    }

    public function get_author(): Player {
        return $this->properties['author'];
    }

    public function get_message(): string {
        return $this->properties['message'];
    }

    public function get_date(): string {
        $this->properties['date'];
    }

    public function get_author_id(): string {
        return $this->properties['game_id'];
    }
}