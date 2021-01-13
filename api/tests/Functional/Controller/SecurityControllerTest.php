<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\UserFixtures;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function setUp(): void
    {
        $this->loadFixtures([UserFixtures::class]);
    }

    public function testLoginAndRefresh(): void
    {
        $token = $this->loginAction();
        $this->refreshToken($token);
    }

    private function loginAction(): string
    {
        //fail
        $client = static::createClient();
        $client->request('POST',
            '/auth/login',
            [],
            [],
            [],
            json_encode([
                'email' => 'wrong',
                'password' => 'wrong',
            ])
        );

        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        //success
        $client = static::createClient();
        $client->request('POST',
            '/auth/login',
            [],
            [],
            [],
            json_encode([
                'email' => UserFixtures::USER_1['email'],
                'password' => UserFixtures::USER_1['password'],
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        return $data['token'];
    }

    private function refreshToken(string $token): void
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));
        $client->request('GET', 'auth/refresh');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $token = json_decode($client->getResponse()->getContent(), true)['token'];
        $pieces = explode('.', $token);

        $email = json_decode(base64_decode($pieces[1]), true)['username'];
        $this->assertEquals($email, UserFixtures::USER_1['email']);
    }
}
