<?php


namespace application\models;

class BoardMessage extends Entity
{
    public array $serializable = ['date','message','message_id','reply_to','author'];

    public function __construct(array $data)
    {
        parent::__construct($data);
        if ($this->has_author())
            $this->set_author(new Player($data));
    }

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
        return $this->properties['date'];
    }

    public function get_author_id(): string {
        return $this->properties['game_token'];
    }

    public function has_author(): bool {
        return $this->get_prop('player_id') != null;
    }
}