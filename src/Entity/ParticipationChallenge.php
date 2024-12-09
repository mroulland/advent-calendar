<?php

namespace App\Entity;

use App\Repository\ParticipationChallengeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipationChallengeRepository::class)]
class ParticipationChallenge extends Challenge
{
    #[ORM\Column(type: 'json', nullable: true)]
    #[Assert\Type(type: 'array', message: 'La valeur doit être un tableau.')]
    private array $questions = [];

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function setQuestions(array $questions): static
    {
        $this->questions = $questions;

        return $this;
    }
}
