<?php

namespace App\Service;

use App\Entity\Bet;
use App\Entity\Issue;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class BettingService
{
    public function __construct(
        private EntityManagerInterface $em,
        private OddsCalculatorService $oddsCalculator
    ) {}

    public function placeBet(User $user, Issue $issue, float $amount): string
    {
        $event = $issue->getSportEvent();

        // check event is open
        if ($event->getStatus() !== 'PUBLIE') {
            return 'This event is not open for betting.';
        }

        // check event date not passed
        if ($event->getEventDate() < new \DateTime()) {
            return 'This event has already started.';
        }

        // check balance
        if ($user->getBalance() < $amount) {
            return 'Insufficient balance.';
        }

        // create the bet
        $bet = new Bet();
        $bet->setUser($user);
        $bet->setSportEvent($event);
        $bet->setIssue($issue);
        $bet->setAmount($amount);
        $bet->setRecordedOdds($issue->getCurrentOdds());
        $bet->setPlacedAt(new \DateTime());
        $bet->setStatus('EN_ATTENTE');

        // deduct balance
        $user->setBalance($user->getBalance() - $amount);

        // record transaction
        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setType('MISE');
        $transaction->setCreatedAt(new \DateTime());
        $transaction->setUser($user);

        // update issue total
        $issue->setTotalAmountBet($issue->getTotalAmountBet() + $amount);

        $this->em->persist($bet);
        $this->em->persist($transaction);
        $this->em->flush();

        // recalculate odds after bet
        $this->oddsCalculator->recalculate($event);
        $this->em->flush();

        return 'success';
    }
}