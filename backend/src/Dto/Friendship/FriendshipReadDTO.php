<?php

namespace App\Dto\Friendship;

use App\Dto\User\UserReadDTO;
use App\Enum\FriendshipStatus;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;


class FriendshipReadDTO
{
    #[Groups(['read:friendship'])]
    public Uuid $id;

    #[Groups(['read:friendship'])]
    public UserReadDTO $friend;

    #[Groups(['read:friendship'])]
    public FriendshipStatus $status;

    #[Groups(['read:friendship'])]
    public ?\DateTime $createdAt = null;
}
