<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, method: 'hashPassword', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'hashPassword', entity: User::class)]
readonly class UserListener
{
    public function __construct(private UserPasswordHasherInterface $encoder)
    {
    }

    public function hashPassword(User $user): void
    {

        $password = $this->encoder->hashPassword($user, $user->getPassword());

        $user->setPassword($password);
    }
}
