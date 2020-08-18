<?php namespace application\models;

use DateTime;

/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 07/06/2016
 * Time: 23:06
 */
class Player extends Entity
{
    // parametri serializzabili in json
    public array $serializable = ['player_name','story','player_face','level','quests','hours','minutes','banned','fame','infame'];

    public function merge(array $data) {
        $level = intval($data['level']);
        if ($level > $this->get_prop('level')) {
            $this->set_prop('level', $level);
        }

        $story = intval($data['story']);
        if ($story > $this->get_prop('story')) {
            $this->set_prop('story', $story);
        }

        $quests = ($data['quests']);
        if ($quests > $this->get_prop('quests')) {
            $this->set_prop('quests', $quests);
        }

        if ($this->playtime_greater(intval($data['hours']), intval($data['minutes']))) {
            $this->set_prop('hours', intval($data['hours']));
            $this->set_prop('minutes', intval($data['minutes']));
        }
    }

    public function get_id(): int {
        return intval($this->get_prop('player_id'));
    }

    public function get_name(): string {
        return $this->get_prop('player_name');
    }

    public function get_face(): int {
        return $this->get_prop('player_face');
    }

    public function set_face(int $new_face) {
        $this->set_prop('player_face', $new_face);
    }

    public function get_points(): int {
        return $this->get_prop('points');
    }

    public function get_registration_date(): DateTime {
        return $this->get_prop('reg_date');
    }

    public function get_game_id(): string {
        return $this->get_prop('game_id');
    }

    public function get_level(): int {
        return $this->get_prop('level');
    }

    public function is_banned(): bool {
        return $this->get_prop('banned');
    }

    public function get_story(): int {
        return $this -> get_prop('story');
    }

    public function get_quests(): int {
        return $this -> get_prop('quests');
    }

    public function get_fame(): int {
        return $this -> get_prop('fame');
    }

    public function get_infame(): int {
        return $this -> get_prop('infame');
    }

    public function get_playtime() {
        return [
            'hours' => $this->get_prop('hours'),
            'minutes' => $this->get_prop('minutes')
        ];
    }

    public function playtime_greater($hours, $minutes): bool {
        if($hours > $this->get_prop('hours')) return true;
        if($minutes > $this->get_prop('minutes')) return true;
        return false;
    }

    public function add_fame(int $value) {
        $current_fame = intval($this->get_prop('fame'));
        $this->set_prop('fame', $current_fame + $value);
    }

    public function add_infame(int $value) {
        $current_infame = intval($this->get_prop('infame'));
        $this->set_prop('infame', $current_infame + $value);
    }
}