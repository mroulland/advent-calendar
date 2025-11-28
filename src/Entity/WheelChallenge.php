<?php

namespace App\Entity;

use App\Repository\WheelChallengeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WheelChallengeRepository::class)]
class WheelChallenge extends Challenge
{
    #[ORM\Column(type: 'json', nullable: true)]
    private array $rewards = [];

    public function getRewards(): array
    {
        return $this->rewards;
    }

    public function setRewards(array $rewards): static
    {
        $this->rewards = $rewards;

        return $this;
    }
}
