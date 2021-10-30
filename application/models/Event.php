<?php


namespace application\models;


class Event extends Entity
{
    public array $serializable = ['event_id', 'event_name', 'description', 'start_date', 'end_date', 'gold_rate', 'exp_rate',
        'drop_rate', 'ap_rate', 'switch_id'];

    public function getExpRate(): int {
        return $this->get_prop('exp_rate');
    }

    public function getDropRate(): int {
        return $this->get_prop('drop_rate');
    }

    public function getApRate(): int {
        return $this->get_prop('ap_rate');
    }

    public function getGoldRate(): int {
        return $this->get_prop('gold_rate');
    }

    public function getDescription(): string {
        return $this->get_prop('description');
    }
}