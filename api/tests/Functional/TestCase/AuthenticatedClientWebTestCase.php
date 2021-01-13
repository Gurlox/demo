<?php

namespace App\Tests\Functional\TestCase;

use App\DataFixtures\UserFixtures;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class AuthenticatedClientWebTestCase extends WebTestCase
{
    use FixturesTrait;

    /**
     * UserFixtures::USER_1
     */
    protected KernelBrowser $authenticatedClient;

    protected function setUp(): void
    {
        parent::bootKernel();

        $this->loadFixtures([UserFixtures::class]);
        $loginClient = static::createClient();
        $loginClient->request('POST',
            '/auth/login',
            [],
            [],
            [],
            json_encode([
                'email' => UserFixtures::USER_1['email'],
                'password' => UserFixtures::USER_1['password'],
            ])
        );
        $data = json_decode($loginClient->getResponse()->getContent(), true);

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
        $this->authenticatedClient = $client;
    }
}
