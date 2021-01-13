<?php

namespace App\DataFixtures;

use App\Entity\Item;
use App\Entity\Module;
use App\Tests\Unit\ReflectionUtil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ItemFixtures extends Fixture
{
    //MODULE_1 PROJECT_1 USER_2
    const ITEM_1 = [
        'id' => 1,
        'name' => 'name',
        'data' => ['someData' => 'data'],
    ];
    const ITEM_1_CHILD = [
        'id' => 2,
        'name' => 'childName',
        'data' => ['someData' => 'childData'],
    ];
    //MODULE_4 PROJECT_2 USER_2
    const ITEM_2 = [
        'name' => 'item2Name',
        'data' => ['test' => 'data'],
    ];

    public function load(ObjectManager $manager): void
    {
        //MODULE_1
        /** @var Module $module */
        $module = $this->getReference('module1');
        $item = new Item(self::ITEM_1['name'], $module, self::ITEM_1['data']);
        ReflectionUtil::setProperty($item, 'id', self::ITEM_1['id']);
        $child = new Item(self::ITEM_1_CHILD['name'], $module, self::ITEM_1_CHILD['data']);
        ReflectionUtil::setProperty($item, 'id', self::ITEM_1_CHILD['id']);
        $item->addChild($child);
        $manager->persist($item);
        $manager->persist($child);
        $this->addReference('item1', $item);

        //MODULE_2
        $module = $this->getReference('module4');
        $item = new Item(self::ITEM_2['name'], $module, self::ITEM_2['data']);
        $manager->persist($item);

        $manager->flush();
    }
}
