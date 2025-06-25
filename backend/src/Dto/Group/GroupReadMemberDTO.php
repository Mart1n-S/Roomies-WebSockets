<?php

namespace App\Dto\Group;

use App\Dto\User\UserReadDTO;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class GroupReadMemberDTO
{
    #[Groups(['read:group'])]
    public Uuid $id;

    #[Groups(['read:group'])]
    public UserReadDTO $member;

    #[Groups(['read:group'])]
    public string $role;
}
