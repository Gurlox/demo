<?php

namespace App\Voters;

use App\Entity\Module;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ModuleVoter extends Voter
{
    const MANAGE = 'manage';

    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, [self::MANAGE])) {
            return false;
        }

        if (!$subject instanceof Module) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var Module $module */
        $module = $subject;
        /** @var User $user */
        $user = $token->getUser();

        return $module->getPage()->getProject()->getUser() === $user;
    }
}
