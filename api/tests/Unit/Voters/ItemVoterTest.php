<?php

namespace App\Tests\Unit\Voters;

use App\Entity\Item;
use App\Entity\Module;
use App\Entity\Page;
use App\Entity\Project;
use App\Entity\User;
use App\Voters\ItemVoter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ItemVoterTest extends TestCase
{
    const ACCESS_GRANTED = 1;
    const ACCESS_DENIED = -1;

    public function testVoteAccessGranted(): void
    {
        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        /** @var Project|MockObject $project */
        $project = new Project('someName', $user);
        $page = new Page($project);
        $module = new Module('module', MODULE::TYPE_BOXES, $page);
        $item = new Item('name', $module, ['someData' => 'value']);

        $itemVoter = new ItemVoter();
        $result = $itemVoter->vote($this->getToken($user), $item, [ItemVoter::MANAGE]);

        $this->assertEquals(self::ACCESS_GRANTED, $result);
    }

    public function testVoteAccessDenied(): void
    {
        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        /** @var User|MockObject $differentUser */
        $differentUser = $this->createMock(User::class);
        /** @var Project|MockObject $project */
        $project = new Project('someName', $differentUser);
        $page = new Page($project);
        $module = new Module('module', Module::TYPE_BOXES, $page);
        $item = new Item('name', $module, ['someData' => 'value']);

        $itemVoter = new ItemVoter();
        $result = $itemVoter->vote($this->getToken($user), $item, [ItemVoter::MANAGE]);

        $this->assertEquals(self::ACCESS_DENIED, $result);
    }

    private function getToken(User $user): TokenInterface
    {
        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($user);

        return $token;
    }
}
