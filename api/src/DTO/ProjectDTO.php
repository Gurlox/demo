<?php

namespace App\DTO;

use App\Entity\Project;

class ProjectDTO
{
    public ?int $id;

    public string $name;

    public UserDTO $user;

    public array $pages;

    public function __construct(Project $project, UserDTO $user, array $pages)
    {
        $this->id = $project->getId();
        $this->name = $project->getName();
        $this->user = $user;
        $this->pages = $pages;
    }
}
