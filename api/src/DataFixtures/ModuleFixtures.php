<?php

namespace App\DataFixtures;

use App\Entity\Module;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ModuleFixtures extends Fixture
{
    //PROJECT_1 page1 USER_1
    const MODULE_1 = [
        'name' => 'name',
        'type' => Module::TYPE_BOXES,
    ];
    const MODULE_2 = [
        'name' => 'name2',
        'type' => MODULE::TYPE_BOXES,
    ];
    const MODULE_3 = [
        'name' => 'name3',
        'type' => MODULE::TYPE_BOXES,
    ];
    //PROJECT_2 page2 USER_2
    const MODULE_4 = [
        'name' => 'name4',
        'type' => MODULE::TYPE_BOXES,
    ];

    public function load(ObjectManager $manager): void
    {
        //PROJECT_1
        /** @var Project $project */
        $project = $this->getReference('project');
        $module = new Module(self::MODULE_1['name'], self::MODULE_1['type'], $project->getPages()->first());
        $manager->persist($module);
        $this->addReference('module1', $module);

        $module = new Module(self::MODULE_2['name'], self::MODULE_2['type'], $project->getPages()->first());
        $manager->persist($module);
        $this->addReference('module2', $module);

        $module = new Module(self::MODULE_3['name'], self::MODULE_3['type'], $project->getPages()->first());
        $manager->persist($module);
        $this->addReference('module3', $module);

        //PROJECT_2
        $project = $this->getReference('project2');
        $module = new Module(self::MODULE_4['name'], self::MODULE_4['type'], $project->getPages()->first());
        $manager->persist($module);
        $this->addReference('module4', $module);

        $manager->flush();
    }
}
