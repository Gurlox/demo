<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\ProjectFactory;
use App\Utils\LoggedUserFaker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

class ProjectFixtures extends Fixture
{
    //USER_1
    const PROJECT_1 = [
        'name' => 'Project1',
    ];
    //USER_2
    const PROJECT_2 = [
        'name' => 'Project2',
    ];

    private ProjectFactory $projectFactory;

    private LoggedUserFaker $loggedUserFaker;

    private EntityManagerInterface $entityManager;

    public function __construct(
        LoggedUserFaker $loggedUserFaker,
        EntityManagerInterface $entityManager,
        ProjectFactory $projectFactory
    ) {
        $this->projectFactory = $projectFactory;
        $this->loggedUserFaker = $loggedUserFaker;
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('user1');
        $this->loggedUserFaker->init($user);
        $project = $this->projectFactory->create(self::PROJECT_1['name']);
        $project->setUser($user);
        $manager->persist($project);

        /** @var User $user2 */
        $user2 = $this->getReference('user2');
        $this->loggedUserFaker->init($user2);
        $project2 = $this->projectFactory->create(self::PROJECT_2['name']);
        $project2->setUser($user2);

        $manager->persist($project2);
        $manager->flush();

        $this->addReference('project', $project);
        $this->addReference('project2', $project2);
    }
}
