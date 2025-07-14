<?php

namespace App\Dto\Group;

use Symfony\Component\Serializer\Annotation\Groups;

class GroupReadDTO
{
    #[Groups(['read:group'])]
    public string $id;

    #[Groups(['read:group'])]
    public string $name;

    #[Groups(['read:group'])]
    public bool $isGroup;

    #[Groups(['read:group'])]
    public \DateTimeImmutable $createdAt;

    #[Groups(['read:group'])]
    /** @var GroupReadMemberDTO[] */
    public array $members = [];

    #[Groups(['read:group'])]
    public ?int $unreadCount = null;
}
