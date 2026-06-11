<?php

namespace App\Service;

use App\Entity\Bet;
use App\Entity\Issue;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SportEvent;
class BettingService
{
    public function __construct(
        private EntityManagerInterface $em,
        private OddsCalculatorService $oddsCalculator
    ) {}

    public function placeBet(User $user, Issue $issue, float $amount): string
    {
        $limitError = $this->checkBetLimits($user, $amount);
if ($limitError !== null) {
    return $limitError;
}
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
    public function resolveEvent(SportEvent $event, Issue $winningIssue): void
{
    foreach ($event->getBets() as $bet) {
        if ($bet->getStatus() !== 'EN_ATTENTE') {
            continue;
        }

        if ($bet->getIssue()->getId() === $winningIssue->getId()) {
            // winner
            $bet->setStatus('GAGNE');
            $gain = $bet->getAmount() * $bet->getRecordedOdds();
            $bet->getUser()->setBalance($bet->getUser()->getBalance() + $gain);

            $transaction = new Transaction();
            $transaction->setAmount($gain);
            $transaction->setType('GAIN');
            $transaction->setCreatedAt(new \DateTime());
            $transaction->setUser($bet->getUser());
            $this->em->persist($transaction);
        } else {
            // loser
            $bet->setStatus('PERDU');
        }
    }

    $event->setStatus('TERMINE');
    $this->em->flush();
}
private function checkBetLimits(User $user, float $amount): ?string
{
    $today = new \DateTime('today');
    $weekStart = new \DateTime('monday this week');

    $dailyTotal = 0;
    $weeklyTotal = 0;

    foreach ($user->getBets() as $bet) {
        if ($bet->getPlacedAt() >= $today) {
            $dailyTotal += $bet->getAmount();
        }
        if ($bet->getPlacedAt() >= $weekStart) {
            $weeklyTotal += $bet->getAmount();
        }
    }

    if ($user->getDailyBetLimit() !== null && ($dailyTotal + $amount) > $user->getDailyBetLimit()) {
        return 'You have reached your daily bet limit.';
    }

    if ($user->getWeeklyBetLimit() !== null && ($weeklyTotal + $amount) > $user->getWeeklyBetLimit()) {
        return 'You have reached your weekly bet limit.';
    }

    return null;
}
}