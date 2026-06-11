<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class WalletService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function deposit(User $user, float $amount): void
    {
        $user->setBalance($user->getBalance() + $amount);

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setType('DEPOT');
        $transaction->setCreatedAt(new \DateTime());
        $transaction->setUser($user);

        $this->em->persist($transaction);
        $this->em->flush();
    }

    public function hasSufficientBalance(User $user, float $amount): bool
    {
        return $user->getBalance() >= $amount;
    }
}