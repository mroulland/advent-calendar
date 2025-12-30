<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\WheelChallengeRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WheelChallengeRepository::class)]
class WheelChallenge extends Challenge
{
    #[ORM\Column(type: 'json', nullable: true)]
    #[Assert\Type(type: 'array', message: 'La valeur doit être un tableau.')]
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
