<?php

namespace App\Entity;

use App\Repository\PhotoChallengeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoChallengeRepository::class)]
class PhotoChallenge extends Challenge
{

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $uploadDirectory = null;

    
    public function getUploadDirectory(): ?string
    {
        return $this->uploadDirectory;
    }

    public function setUploadDirectory(string $uploadDirectory): static
    {
        $this->uploadDirectory = $uploadDirectory;

        return $this;
    }
}
