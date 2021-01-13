<?php

namespace App\Tests\Unit\Factory\DTO;

use App\DTO\ProjectDTO;
use App\DTO\UserDTO;
use App\Entity\Project;
use App\Entity\User;
use App\Factory\DTO\PageDTOFactory;
use App\Factory\DTO\ProjectDTOFactory;
use App\Factory\DTO\UserDTOFactory;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ProjectDTOFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $pageDTOFactory = $this->createMock(PageDTOFactory::class);
        $pageDTO = $this->createMock(ProjectDTO::class);
        $pageDTOFactory->method('create')->willReturn($pageDTO);

        $userDTOFactory = $this->createMock(UserDTOFactory::class);
        $userDTO = $this->createMock(UserDTO::class);
        $userDTOFactory->method('create')->willReturn($userDTO);

        $projectDTOFactory = new ProjectDTOFactory($pageDTOFactory, $userDTOFactory);
        $project = $this->createMock(Project::class);
        $id = 1;
        $name = 'name';
        $project->method('getId')->willReturn($id);
        $project->method('getName')->willReturn($name);
        $project->method('getUser')->willReturn($this->createMock(User::class));
        $project->method('getPages')->willReturn(new ArrayCollection());

        $result = $projectDTOFactory->create($project);
        $this->assertEquals($result->id, $id);
        $this->assertEquals($result->user, $userDTO);
        $this->assertEquals($result->name, $name);
    }
}
