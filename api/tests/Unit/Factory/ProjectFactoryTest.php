<?php

namespace App\Tests\Unit\Factory;

use App\Entity\User;
use App\Factory\ProjectFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class ProjectFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $user = $this->createMock(User::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $expectedName = 'name';
        $projectFactory = new ProjectFactory($security);
        $project = $projectFactory->create($expectedName);

        $this->assertEquals($project->getName(), $expectedName);
        $this->assertEquals($project->getUser(), $user);
    }
}
