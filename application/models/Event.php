<?php


namespace application\models;


class Event extends Entity
{
    public array $serializable = ['event_id', 'event_name', 'start_date', 'end_date', 'gold_rate', 'exp_rate',
        'drop_rate', 'jp_rate', 'switch_id'];

    public function getExpRate(): int {
        return $this->get_prop('exp_rate');
    }

    public function getDropRate(): int {
        return $this->get_prop('drop_rate');
    }

    public function getJpRate(): int {
        return $this->get_prop('jp_rate');
    }

    public function getGoldRate(): int {
        return $this->get_prop('gold_rate');
    }
}