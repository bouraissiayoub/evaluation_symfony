<?php

namespace App\Entity;

use App\Repository\IssueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IssueRepository::class)]
class Issue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column]
    private float $currentOdds = 1.50;

    #[ORM\Column]
    private ?float $totalAmountBet =  0.0;

    #[ORM\ManyToOne(inversedBy: 'issues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SportEvent $sportEvent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getCurrentOdds(): ?float
    {
        return $this->currentOdds;
    }

    public function setCurrentOdds(float $currentOdds): static
    {
        $this->currentOdds = $currentOdds;

        return $this;
    }

    public function getTotalAmountBet(): ?float
    {
        return $this->totalAmountBet;
    }

    public function setTotalAmountBet(float $totalAmountBet): static
    {
        $this->totalAmountBet = $totalAmountBet;

        return $this;
    }

    public function getSportEvent(): ?SportEvent
    {
        return $this->sportEvent;
    }

    public function setSportEvent(?SportEvent $sportEvent): static
    {
        $this->sportEvent = $sportEvent;

        return $this;
    }
}
