<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use App\Entity\Bet;
final class BetPlacedListener
{
    #[AsEventListener]
    public function onRequestEvent(RequestEvent $event): void
    {
        // ...
    }
}
