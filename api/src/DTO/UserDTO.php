<?php

namespace App\DTO;

use App\Entity\User;

class UserDTO
{
    public string $email;

    public function __construct(User $user)
    {
        $this->email = $user->getEmail();
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
