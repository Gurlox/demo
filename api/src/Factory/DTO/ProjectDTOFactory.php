<?php

namespace App\Factory\DTO;

use App\DTO\PageDTO;
use App\DTO\ProjectDTO;
use App\Entity\Project;

class ProjectDTOFactory
{
    private PageDTOFactory $pageDTOFactory;

    private UserDTOFactory $userDTOFactory;

    public function __construct(PageDTOFactory $pageDTOFactory, UserDTOFactory $userDTOFactory)
    {
        $this->pageDTOFactory = $pageDTOFactory;
        $this->userDTOFactory = $userDTOFactory;
    }

    public function create(Project $project): ProjectDTO
    {
        $pages = [];
        foreach ($project->getPages() as $page) {
            $pages[] = $this->pageDTOFactory->create($page);
        }
        $projectDTO = new ProjectDTO(
            $project,
            $this->userDTOFactory->create($project->getUser()),
            $pages
        );

        return $projectDTO;
    }
}
