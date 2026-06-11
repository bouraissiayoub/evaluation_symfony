<?php

namespace App\EventSubscriber;

use App\Entity\Bet;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

#[AsEntityListener(event: Events::postPersist, entity: Bet::class)]
class BetSubscriber
{
    public function __construct(private LoggerInterface $logger) {}

    public function postPersist(Bet $bet): void
    {
        $this->logger->info('New bet placed', [
            'user' => $bet->getUser()->getEmail(),
            'amount' => $bet->getAmount(),
            'event' => $bet->getSportEvent()->getName(),
        ]);
    }
}