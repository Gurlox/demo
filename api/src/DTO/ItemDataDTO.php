<?php

namespace App\DTO;

class ItemDataDTO
{
    public $fields = [];

    public function __get(string $name)
    {
        return $this->fields[$name] ?? null;
    }

    public function __set(string $name, $value)
    {
        $this->fields[$name] = $value;
    }
}
