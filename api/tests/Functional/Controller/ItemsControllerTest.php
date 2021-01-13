<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\ItemFixtures;
use App\DataFixtures\ModuleFixtures;
use App\DataFixtures\ProjectFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Item;
use App\Tests\Functional\TestCase\AuthenticatedClientWebTestCase;
use Doctrine\ORM\EntityManager;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class ItemsControllerTest extends AuthenticatedClientWebTestCase
{
    use FixturesTrait;

    private EntityManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([UserFixtures::class, ProjectFixtures::class, ModuleFixtures::class, ItemFixtures::class]);
        $this->entityManager = parent::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testPatchItems(): void
    {
        $client = $this->authenticatedClient;
        $item = $this->entityManager
            ->getRepository(Item::class)
            ->findOneBy(['name' => ItemFixtures::ITEM_1['name']])
        ;
        $itemId = $item->getId();
        $newData = ['data' => 'newData'];

        $client->request(
            'PATCH',
            "/items/$itemId",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($newData)
        );

        $content = $client->getResponse()->getContent();

        $this->assertJson($content);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_decode($content, true)['item']['data'], $newData['data']);
    }

    public function testPatchItemsAccessDenied(): void
    {
        $client = $this->authenticatedClient;
        /** @var Item $item */
        $item = $this->entityManager
            ->getRepository(Item::class)
            ->findOneBy(['name' => ItemFixtures::ITEM_2['name']])
        ;
        $itemId = $item->getId();

        $client->request(
            'PATCH',
            "/items/$itemId",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
