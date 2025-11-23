<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class HangmanChallenge extends Challenge
{
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $word;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $hint = null;

    #[ORM\Column(type: 'integer')]
    private int $maxErrors = 6;

    public function getWord(): string
    {
        return $this->word;
    }

    public function setWord(string $word): self
    {
        $this->word = $word;

        return $this;
    }

    public function getHint(): ?string
    {
        return $this->hint;
    }

    public function setHint(?string $hint): self
    {
        $this->hint = $hint;

        return $this;
    }

    public function getMaxErrors(): int
    {
        return $this->maxErrors;
    }

    public function setMaxErrors(int $maxErrors): self
    {
        $this->maxErrors = $maxErrors;

        return $this;
    }
}
