<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class PasswordResetService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function generateResetToken(User $user, int $ttlSeconds = 3600): string
    {
        // token en clair
        $token = bin2hex(random_bytes(32)); // 64 caractères hex

        // hash stocké en base
        $hash = password_hash($token, PASSWORD_DEFAULT);

        $user->setTokenHash($hash);
        $user->setTokenHashExpiresAt(
            (new \DateTimeImmutable())->modify("+{$ttlSeconds} seconds")
        );

        $this->em->flush();

        return $token; // on le renverra au mailer
    }



    public function clearToken(User $user): void
    {
        $user->setTokenHash(null);
        $user->setTokenHashExpiresAt(null);
        $this->em->flush();
    }
}