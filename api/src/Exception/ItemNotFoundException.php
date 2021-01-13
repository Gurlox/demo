<?php

namespace App\Exception;

class ItemNotFoundException extends \InvalidArgumentException
{
    public function __construct(?int $id)
    {
        parent::__construct($id);
    }
}
