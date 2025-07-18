<?php

namespace App\Service;

use App\Entity\User;

class AvatarUrlGeneratorService
{
    public function __construct(
        private readonly string $avatarPublicPath,
        private readonly string $baseUrl
    ) {}

    public function generate(?User $user): string
    {
        $avatarFile = $user?->getImageName() ?: 'default-avatar.svg';
        return rtrim($this->baseUrl . $this->avatarPublicPath, '/') . '/' . $avatarFile;
    }
}
