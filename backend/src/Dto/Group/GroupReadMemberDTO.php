<?php

namespace App\Dto\Group;

use App\Dto\User\UserReadDTO;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class GroupReadMemberDTO
{
    #[Groups(['read:group', 'read:friendshipWithRoom'])]
    public Uuid $id;

    #[Groups(['read:group', 'read:friendshipWithRoom'])]
    public UserReadDTO $member;

    #[Groups(['read:group', 'read:friendshipWithRoom'])]
    public bool $isVisible;

    #[Groups(['read:group', 'read:friendshipWithRoom'])]
    public string $role;
}
