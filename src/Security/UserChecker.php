<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAuthenticationException(
                'Your account has been suspended.'
            );
        }

        if ($user->getSelfExclusionEnd() !== null && $user->getSelfExclusionEnd() > new \DateTime()) {
            throw new CustomUserMessageAuthenticationException(
                'You have self-excluded until ' . $user->getSelfExclusionEnd()->format('d/m/Y')
            );
        }
    }

public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void {}
 
}