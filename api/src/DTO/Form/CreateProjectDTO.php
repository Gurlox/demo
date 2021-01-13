<?php

namespace App\DTO\Form;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProjectDTO
{
    /**
     * @Assert\NotBlank(message="create_project.name_not_blank")
     * @Assert\Length(
     *     min=3,
     *     max=100,
     *     minMessage="create_project.name_min_length",
     *     maxMessage="create_project.name_max_length"
     * )
     */
    public string $name;
}