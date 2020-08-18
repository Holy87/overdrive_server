<?php namespace utils;

class Playtime implements \JsonSerializable
{
    public int $int_value;
    public int $hours;
    public int $minutes;
    public int $seconds;

    public function __construct(int $hours, int $minutes, int $seconds)
    {
        $this->hours = $hours;
        $this->minutes = $minutes;
        $this->seconds = $seconds;
        $this->int_value = $hours * 3600 + $minutes * 60 + $seconds;
    }

    public function jsonSerialize()
    {
        return [
            'hours' => $this->hours,
            'minutes' => $this->minutes,
            'seconds' => $this->seconds
        ];
    }
}