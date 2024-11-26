<?php

namespace App\Entity;

use App\Repository\ChallengeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChallengeRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "type", type: "string")]
#[ORM\DiscriminatorMap(["quiz" => QuizChallenge::class, "photo" => PhotoChallenge::class])]
class Challenge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * @var Collection<int, Ranking>
     */
    #[ORM\OneToMany(targetEntity: Ranking::class, mappedBy: 'challenge')]
    private Collection $rankings;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\OneToOne(mappedBy:"challenge", targetEntity:Calendar::class, cascade: ['persist', 'remove'])]
    private ?Calendar $calendar = null;

    public function __construct()
    {
        $this->rankings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Ranking>
     */
    public function getRankings(): Collection
    {
        return $this->rankings;
    }

    public function addRanking(Ranking $ranking): static
    {
        if (!$this->rankings->contains($ranking)) {
            $this->rankings->add($ranking);
            $ranking->setChallenge($this);
        }

        return $this;
    }

    public function removeRanking(Ranking $ranking): static
    {
        if ($this->rankings->removeElement($ranking)) {
            if ($ranking->getChallenge() === $this) {
                $ranking->setChallenge(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCalendar(): ?Calendar
    {
        return $this->calendar;
    }

    public function setCalendar(?Calendar $calendar): static
    {
        $this->calendar = $calendar;
        if ($calendar !== null && $calendar->getChallenge() !== $this) {
            $calendar->setChallenge($this);
        }
        return $this;
    }
}
