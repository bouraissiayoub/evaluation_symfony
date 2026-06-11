<?php

namespace App\Service;

use App\Entity\Issue;
use App\Entity\SportEvent;

class OddsCalculatorService
{
    public function recalculate(SportEvent $event): void
    {
        $totalOnEvent = 0;
        foreach ($event->getIssues() as $issue) {
            $totalOnEvent += $issue->getTotalAmountBet();
        }

        if ($totalOnEvent === 0.0) {
            return;
        }

        foreach ($event->getIssues() as $issue) {
            if ($issue->getTotalAmountBet() === 0.0) {
                continue;
            }

            $odds = $totalOnEvent / $issue->getTotalAmountBet();
            $odds = max(1.10, min(5.00, $odds));
            $issue->setCurrentOdds($odds);
        }
    }
}