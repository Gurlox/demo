<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\ProjectFixtures;
use App\DataFixtures\UserFixtures;
use App\Tests\Functional\TestCase\AuthenticatedClientWebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class ProjectsControllerTest extends AuthenticatedClientWebTestCase
{
    use FixturesTrait;

    public function testPostProjectsSuccess(): void
    {
        $client = $this->authenticatedClient;
        $client->request('POST', '/projects', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'name'
        ]));

        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPostProjectsFailure(): void
    {
        $client = $this->authenticatedClient;
        $client->request('POST', '/projects', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => ''
        ]));

        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testGetProjects(): void
    {
        $this->loadFixtures([UserFixtures::class, ProjectFixtures::class]);
        $client = $this->authenticatedClient;
        $client->request('GET', '/projects');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
    }
}
