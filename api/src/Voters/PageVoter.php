<?php

namespace App\Voters;

use App\Entity\Page;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PageVoter extends Voter
{
    const MANAGE = 'manage';

    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, [self::MANAGE])) {
            return false;
        }

        if (!$subject instanceof Page) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var Page $page */
        $page = $subject;
        /** @var User $user */
        $user = $token->getUser();

        return $page->getProject()->getUser() === $user;
    }
}
