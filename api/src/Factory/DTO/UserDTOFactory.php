<?php

namespace App\Factory\DTO;

use App\DTO\UserDTO;
use App\Entity\User;

class UserDTOFactory
{
    public function create(User $user): UserDTO
    {
        return new UserDTO($user);
    }
}
