<?php

namespace App\Dto\Group;

use Symfony\Component\Serializer\Annotation\Groups;

class GroupReadDTO
{
    #[Groups(['read:group', 'read:friendshipWithRoom'])]
    public string $id;

    #[Groups(['read:group', 'read:friendshipWithRoom'])]
    public string $name;

    #[Groups(['read:group', 'read:friendshipWithRoom'])]
    public bool $isGroup;

    #[Groups(['read:group', 'read:friendshipWithRoom'])]
    public \DateTimeImmutable $createdAt;

    #[Groups(['read:group', 'read:friendshipWithRoom'])]
    /** @var GroupReadMemberDTO[] */
    public array $members = [];

    #[Groups(['read:group'])]
    public ?int $unreadCount = null;
}
