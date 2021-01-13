<?php

namespace App\Factory;

use App\Entity\Page;
use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class ProjectFactory
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function create(string $name): Project
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $project = new Project($name, $user);
        $page = new Page($project);
        $project->addPage($page);

        return $project;
    }
}
