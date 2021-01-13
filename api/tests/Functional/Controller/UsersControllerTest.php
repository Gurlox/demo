<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\UserFixtures;
use App\Tests\Functional\TestCase\AuthenticatedClientWebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class UsersControllerTest extends AuthenticatedClientWebTestCase
{
    use FixturesTrait;

    public function testPostUsersSuccess(): void
    {
        $this->postUser(200, [
            'email' => 'test@test.pl',
            'password' => ['first' => 'password', 'second' => 'password']
        ]);

        //fail - different passwords
        $response = $this->postUser(400, [
            'email' => 'test2@test.pl',
            'password' => ['first' => 'password2', 'second' => 'password']
        ]);
        $this->assertTrue(isset($response['messages']['password']));

        //fail - invalid email
        $response = $this->postUser(400, [
            'email' => 'invalid_email',
            'password' => ['first' => 'password', 'second' => 'password']
        ]);
        $this->assertTrue(isset($response['messages']['email']));

        //fail - non unique email
        $response = $this->postUser(400, [
            'email' => UserFixtures::USER_1['email'],
            'password' => ['first' => 'password', 'second' => 'password']
        ]);
        $this->assertTrue(isset($response['messages']['email']));
    }

    public function testGetAuthenticatedUser(): void
    {
        $this->authenticatedClient->request('GET', '/auth/user');

        $response = json_decode($this->authenticatedClient->getResponse()->getContent(), true);
        $this->assertNotNull($response['user']);
    }

    private function postUser(int $assertion, array $body): array
    {
        $client = static::createClient();
        $client->request('POST', '/public/users', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($body));

        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals($assertion, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }
}
