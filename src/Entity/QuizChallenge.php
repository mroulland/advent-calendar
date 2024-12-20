<?php

namespace App\Entity;

use App\Repository\QuizChallengeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuizChallengeRepository::class)]
class QuizChallenge extends Challenge
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
