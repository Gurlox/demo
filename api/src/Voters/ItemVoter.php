<?php

namespace App\Voters;

use App\Entity\Item;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ItemVoter extends Voter
{
    const MANAGE = 'manage';

    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, [self::MANAGE])) {
            return false;
        }

        if (!$subject instanceof Item) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var Item $item */
        $item = $subject;
        /** @var User $user */
        $user = $token->getUser();

        return $item->getModule()->getPage()->getProject()->getUser() === $user;
    }
}
