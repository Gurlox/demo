<?php

namespace App\Tests\Unit\Service;

use App\DTO\ProjectDTO;
use App\Entity\Project;
use App\Entity\User;
use App\Factory\DTO\ProjectDTOFactory;
use App\Factory\ProjectFactory;
use App\Service\ProjectService;
use App\Tests\Unit\TestCase\ValidationTypeTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Security;

class ProjectServiceTest extends ValidationTypeTestCase
{
    /**
     * @var ProjectFactory|MockObject
     */
    private MockObject $projectFactory;
    /**
     * @var EntityManagerInterface|MockObject
     */
    private MockObject $em;
    /**
     * @var ProjectDTOFactory|MockObject
     */
    private MockObject $projectDTOFactory;
    /**
     * @var MockObject|Security
     */
    private MockObject $security;

    public function __construct()
    {
        parent::__construct();
        $this->projectFactory = $this->createMock(ProjectFactory::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->em->method('persist')->willReturn(null);
        $this->em->method('flush')->willReturn(null);
        $this->projectDTOFactory = $this->createMock(ProjectDTOFactory::class);
        $this->security = $this->createMock(Security::class);
    }

    public function testCreate(): void
    {
        $project = $this->createMock(Project::class);
        $this->projectFactory->method('create')->willReturn($project);

        $projectDTO = $this->createMock(ProjectDTO::class);
        $this->projectDTOFactory->method('create')->willReturn($projectDTO);

        $result = $this->createProjectService()->create(['name' => 'valid']);
        $this->assertEquals($result, $projectDTO);
    }

    public function testGetLoggedUserProjects(): void
    {
        $collection = new ArrayCollection();
        $project = $this->createMock(Project::class);
        $project2 = clone $project;
        $collection->add($project);
        $collection->add($project2);

        $user = $this->createMock(User::class);
        $user->method('getProjects')->willReturn($collection);
        $this->security->method('getUser')->willReturn($user);

        $this->projectDTOFactory->method('create')
            ->willReturn($this->createMock(ProjectDTO::class));

        $result = $this->createProjectService()->getLoggedUserProjects();
        $this->assertTrue(is_array($result));
    }

    private function createProjectService(): ProjectService
    {
        return new ProjectService(
            $this->projectFactory,
            $this->factory,
            $this->em,
            $this->projectDTOFactory,
            $this->security
        );
    }
}
