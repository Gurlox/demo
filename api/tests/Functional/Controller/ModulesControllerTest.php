<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\ItemFixtures;
use App\DataFixtures\ModuleFixtures;
use App\DataFixtures\ProjectFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Item;
use App\Entity\Module;
use App\Entity\Page;
use App\Entity\Project;
use App\Tests\Functional\TestCase\AuthenticatedClientWebTestCase;
use App\Tests\Unit\DataProviders\ModuleProvider;
use App\Tests\Unit\DataProviders\ModulesListProvider;
use Doctrine\ORM\EntityManager;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class ModulesControllerTest extends AuthenticatedClientWebTestCase
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

    public function testPostModuleAccessDenied(): void
    {
        $client = $this->authenticatedClient;
        /** @var Page $page */
        $page = $this->entityManager
            ->getRepository(Project::class)
            ->findOneBy(['name' => ProjectFixtures::PROJECT_2['name']])
        ;
        $pageId = $page->getId();

        $client->request(
            'POST',
            "/pages/$pageId/modules",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testPostModulesSuccess(): void
    {
        $client = $this->authenticatedClient;
        /** @var Page $page */
        $page = $this->entityManager
            ->getRepository(Project::class)
            ->findOneBy(['name' => ProjectFixtures::PROJECT_1['name']])
        ;
        $pageId = $page->getId();

        $client->request(
            'POST',
            "/pages/$pageId/modules",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(ModulesListProvider::getModulesListSample())
        );

        $decodedContent = json_decode($client->getResponse()->getContent(), true);
        //because first modules are from fixtures
        $lastModule = end($decodedContent['modules']);

        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($lastModule['slug'], ModulesListProvider::getFirstModuleSample()['slug']);
        $this->assertEquals($lastModule['showInMenu'], ModulesListProvider::getFirstModuleSample()['showInMenu']);
    }

    public function testGetModulesAccessDenied(): void
    {
        $client = $this->authenticatedClient;
        /** @var Page $page */
        $page = $this->entityManager
            ->getRepository(Project::class)
            ->findOneBy(['name' => ProjectFixtures::PROJECT_2['name']])
        ;
        $pageId = $page->getId();

        $client->request('GET', "/pages/$pageId/modules");

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testGetModules(): void
    {
        $client = $this->authenticatedClient;
        /** @var Page $page */
        $page = $this->entityManager
            ->getRepository(Project::class)
            ->findOneBy(['name' => ProjectFixtures::PROJECT_1['name']])
        ;
        $pageId = $page->getId();

        $client->request('GET', "/pages/$pageId/modules");

        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDeleteModule(): void
    {
        $this->loadFixtures([UserFixtures::class, ProjectFixtures::class, ModuleFixtures::class, ItemFixtures::class]);
        $client = $this->authenticatedClient;
        $moduleId = $this->getFixturesModule(1)->getId();

        $client->request('DELETE', "/modules/$moduleId");

        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNull($this->getFixturesModule(1));
    }

    public function testDeleteModuleExpectsAccessDenied(): void
    {
        $client = $this->authenticatedClient;
        /** @var Module $module */
        $module = $this->entityManager
            ->getRepository(Module::class)
            ->findOneBy(['name' => ModuleFixtures::MODULE_4['name']])
        ;
        $moduleId = $module->getId();

        $client->request('DELETE', "/modules/$moduleId");

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testPostOrderChangeUp(): void
    {
        $this->loadFixtures([UserFixtures::class, ProjectFixtures::class, ModuleFixtures::class]);
        $client = $this->authenticatedClient;
        //position 1
        $moduleId = $this->getFixturesModule(2)->getId();

        $client->request('POST', "/modules/$moduleId/sort/up");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(0, $this->getFixturesModule(2)->getPosition());
        $this->assertEquals(1, $this->getFixturesModule(1)->getPosition());
    }

    public function testPostOrderChangeDown(): void
    {
        $this->loadFixtures([UserFixtures::class, ProjectFixtures::class, ModuleFixtures::class]);
        $client = $this->authenticatedClient;
        //position 1
        $moduleId = $this->getFixturesModule(2)->getId();

        $client->request('POST', "/modules/$moduleId/sort/down");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $this->getFixturesModule(3)->getPosition());
        $this->assertEquals(2, $this->getFixturesModule(2)->getPosition());
    }

    public function testPostOrderInvalidParameter(): void
    {
        $this->loadFixtures([UserFixtures::class, ProjectFixtures::class, ModuleFixtures::class]);
        $client = $this->authenticatedClient;
        $moduleId = $this->getFixturesModule(2)->getId();

        $client->request('POST', "/modules/$moduleId/sort/wrongParam");

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testPostOrderOutOfRange(): void
    {
        $this->loadFixtures([UserFixtures::class, ProjectFixtures::class, ModuleFixtures::class]);
        $client = $this->authenticatedClient;
        $moduleId = $this->getFixturesModule(1)->getId();

        $client->request('POST', "/modules/$moduleId/sort/up");

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testPatchModules(): void
    {
        $this->loadFixtures([UserFixtures::class, ProjectFixtures::class, ModuleFixtures::class, ItemFixtures::class]);
        $client = $this->authenticatedClient;
        $moduleId = $this->getFixturesModule(1)->getId();
        $moduleSample = ModuleProvider::getModuleSample();
        $firstItemSample = ModuleProvider::getFirstItemSample();
        $firstChildSample = ModuleProvider::getFirstChildItemSample();

        $client->request(
            'PATCH',
            "/modules/$moduleId",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($moduleSample)
        );

        $content = json_decode($client->getResponse()->getContent(), true)['module'];

        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($moduleSample['showInMenu'], $content['showInMenu']);
        $this->assertEquals($moduleSample['labelInMenu'], $content['labelInMenu']);
        $this->assertEquals($moduleSample['slug'], $content['slug']);
        $this->assertEquals($firstItemSample['data']['text'], $content['items'][ItemFixtures::ITEM_1['name']]['text']);
        $this->assertEquals($firstChildSample['data']['text'], $content['items'][ItemFixtures::ITEM_1['name']]['items'][ItemFixtures::ITEM_1_CHILD['name']]['text']);

        $updatedModule = $this->getFixturesModule(1);
        /** @var Item $updatedItem */
        $updatedItem = $this->entityManager->getRepository(Item::class)->find(ItemFixtures::ITEM_1['id']);
        /** @var Item $updatedChild */
        $updatedChild = $this->entityManager->getRepository(Item::class)->find(ItemFixtures::ITEM_1_CHILD['id']);

        $this->assertEquals($moduleSample['showInMenu'], $updatedModule->getShowInMenu());
        $this->assertEquals($moduleSample['labelInMenu'], $updatedModule->getLabelInMenu());
        $this->assertEquals($moduleSample['slug'], $updatedModule->getSlug());
        $this->assertEquals($firstItemSample['data'], $updatedItem->getData());
        $this->assertEquals($firstChildSample['data'], $updatedChild->getData());
    }

    public function testPatchModulesWithNonExistingItem(): void
    {
        $this->loadFixtures([UserFixtures::class, ProjectFixtures::class, ModuleFixtures::class, ItemFixtures::class]);
        $client = $this->authenticatedClient;
        $moduleId = $this->getFixturesModule(1)->getId();
        $moduleSample = ModuleProvider::getModuleSample();
        $moduleSample['items'][] = ['id' => 99999];

        $client->request(
            'PATCH',
            "/modules/$moduleId",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($moduleSample)
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    private function getFixturesModule(int $number): ?Module
    {
        return $this->entityManager
            ->getRepository(Module::class)
            ->findOneBy(['name' => constant(ModuleFixtures::class.'::MODULE_'.$number)['name']])
            ;
    }
}
