<?php namespace application\models;

class Entity implements \JsonSerializable {
    public array $properties = [];
    public array $attributes = [];
    public array $serializable = [];

    public function __construct(array $data) {
        foreach (array_keys($data) as $prop) {
            if(isset($data[$prop])) $this->properties[$prop] = $data[$prop];
        }
    }

    public function get_prop(string $prop_name) {
        if (!isset($this->properties[$prop_name])) return null;
        return $this->properties[$prop_name];
    }

    public function set_prop(string $prop_name, $value) {
        $this->properties[$prop_name] = $value;
    }

    public function jsonSerialize()
    {
        if (!AUTO_ENCODE_BASE64) header('Content-Type: application/json');
        $output = [];
        foreach ($this->serializable as $prop) {
            $output[$prop] = isset($this->properties[$prop]) ? $this->properties[$prop] : null;
        }
        return $output;
    }
}