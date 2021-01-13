<?php

namespace App\Tests\Unit\Service;

use App\DTO\ItemDTO;
use App\DTO\ModuleDTO;
use App\Entity\Module;
use App\Entity\Page;
use App\Entity\Project;
use App\Factory\ModuleFactory;
use App\Service\ItemService;
use App\Service\ModuleService;
use App\Tests\Unit\DataProviders\ModuleProvider;
use App\Tests\Unit\DataProviders\ModulesListProvider;
use App\Tests\Unit\ReflectionUtil;
use App\Tests\Unit\TestCase\ValidationTypeTestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

class ModuleServiceTest extends ValidationTypeTestCase
{
    /**
     * @var ModuleFactory|MockObject
     */
    private MockObject$moduleFactory;
    /**
     * @var EntityManagerInterface|MockObject
     */
    private MockObject $em;
    /**
     * @var ItemService|MockObject
     */
    private MockObject $itemService;

    public function __construct()
    {
        parent::__construct();
        $this->moduleFactory = $this->createMock(ModuleFactory::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->itemService = $this->createMock(ItemService::class);
    }

    public function testCreateFromDefaultConfiguration(): void
    {
        $data = ModulesListProvider::getModulesListSample();
        $this->em->method('persist')->willReturn(null);
        $this->em->method('flush')->willReturn(null);
        $module = $this->createMock(Module::class);

        $this->moduleFactory->method('createFromDTO')->willReturn($module);
        $page = $this->createMock(Page::class);
        $page->method('addModule')->willReturn($page);
        $modules = $this->getModuleService()->createFromDefaultConfiguration($data, $page);

        /** @var ModuleDTO $module */
        $module = $modules->modules->first();
        /** @var ItemDTO $firstItem */
        $firstItem = $module->getItems()->first();
        /** @var ItemDTO $childItem */
        $childItem = $firstItem->getItems()->first();

        $this->assertEquals($module->getName(), ModulesListProvider::getFirstModuleSample()['name']);
        $this->assertEquals($firstItem->getName(), ModulesListProvider::getFirstItemSample()['name']);
        $this->assertEquals($childItem->getName(), ModulesListProvider::getFirstChildItemSample()['name']);
    }

    public function testNormalizeModulesForPage(): void
    {
        $page = new Page($this->createMock(Project::class));
        $module = new Module('name', Module::TYPE_BOXES, $page);
        $page->addModule($module);

        $this->itemService->method('getItemsList')->willReturn([]);

        $result = $this->getModuleService()->normalizeModulesForPage($page);
        $this->assertEquals($module->getName(), $result[0]['name']);
        $this->assertEquals($module->getType(), $result[0]['type']);
    }

    public function testNormalizeModule(): void
    {
        $this->itemService->method('getItemsList')->willReturn([]);
        $module = new Module('name', Module::TYPE_BOXES, $this->createMock(Page::class));
        ReflectionUtil::setProperty($module, 'id', 1);
        $module
            ->setShowInMenu(true)
            ->setSlug('slug')
            ->setLabelInMenu('label')
        ;

        $result = $this->getModuleService()->normalizeModule($module);

        $this->assertEquals($module->getId(), $result['id']);
        $this->assertEquals($module->getName(), $result['name']);
        $this->assertEquals($module->getType(), $result['type']);
        $this->assertEquals($module->getShowInMenu(), $result['showInMenu']);
        $this->assertEquals($module->getSlug(), $result['slug']);
        $this->assertEquals($module->getLabelInMenu(), $result['labelInMenu']);
        $this->assertEquals([], $result['items']);
    }

    public function testChangeOrder(): void
    {
        $page = $this->createMock(Page::class);
        $module = new Module('exampleModule', Module::TYPE_BOXES, $page);

        $this->em->method('flush')->willReturn(null);
        $this->getModuleService()->changeOrder($module, ModuleService::SORT_DOWN);
        $this->assertEquals(1, $module->getPosition());

        $this->getModuleService()->changeOrder($module, ModuleService::SORT_UP);
        $this->assertEquals(0, $module->getPosition());

        $this->expectException(\InvalidArgumentException::class);
        $this->getModuleService()->changeOrder($module, 'wrong');
    }

    public function testModify(): void
    {
        $this->itemService->method('updateItemsFromDTO')->willReturn(null);
        $this->em->method('flush')->willReturn(null);

        $module = (new Module('name', Module::TYPE_BOXES, $this->createMock(Page::class)))
            ->setLabelInMenu('oldLabel')
            ->setShowInMenu(false)
            ->setSlug('oldSlug')
        ;
        $moduleSample = ModuleProvider::getModuleSample();
        $this->getModuleService()->modify($moduleSample, $module);

        $this->assertEquals($moduleSample['showInMenu'], $module->getShowInMenu());
        $this->assertEquals($moduleSample['labelInMenu'], $module->getLabelInMenu());
        $this->assertEquals($moduleSample['slug'], $module->getSlug());
    }

    private function getModuleService(): ModuleService
    {
        return new ModuleService($this->factory, $this->moduleFactory, $this->em, $this->itemService);
    }
}
