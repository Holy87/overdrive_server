<?php


namespace application\models;

class BoardMessage extends Entity
{
    public array $serializable = ['date','message','message_id','old_player_name','reply_to','author'];

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
        return base64_decode($this->properties['message']);
    }

    public function get_date(): string {
        return $this->properties['date'];
    }

    public function get_author_id(): int {
        if ($this->has_author()) {
            return $this->get_author()->get_id();
        } else {
            return 0;
        }
    }

    public function get_author_name(): string {
        if ($this->has_author()) {
            return $this->get_author()->get_name();
        } else {
            return $this->get_prop('old_player_name');
        }
    }

    public function get_old_player_token(): string {
        return $this->get_prop('legacy_game_id');
    }

    public function has_author(): bool {
        return $this->get_prop('player_id') != null;
    }

    public function get_id(): int {
        return $this->get_prop('message_id');
    }
}