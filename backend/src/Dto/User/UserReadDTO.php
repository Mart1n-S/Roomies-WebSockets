<?php

namespace App\Dto\User;

use Symfony\Component\Serializer\Annotation\Groups;

final class UserReadDTO
{
    #[Groups(['read:me'])]
    public ?string $email = null;

    #[Groups(['read:me'])]
    public ?array $roles = null;

    #[Groups(['read:me', 'read:user', 'read:friendship', 'read:group'])]
    public ?string $pseudo = null;

    #[Groups(['read:me', 'read:user', 'read:friendship', 'read:group'])]
    public ?string $avatar = null;

    #[Groups(['read:me', 'read:user', 'read:friendship', 'read:group'])]
    public ?string $friendCode = null;
}
