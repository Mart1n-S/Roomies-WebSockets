<?php

namespace App\Dto\Friendship;

use App\Dto\Group\GroupReadDTO;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use App\Dto\User\UserReadDTO;
use App\Enum\FriendshipStatus;

/**
 * DTO retourné lorsqu'une demande d'ami est acceptée,
 * incluant la relation + la room privée.
 */
class FriendshipWithRoomReadDTO
{
    #[Groups(['read:friendshipWithRoom'])]
    public Uuid $id;

    #[Groups(['read:friendshipWithRoom'])]
    public UserReadDTO $friend;

    #[Groups(['read:friendshipWithRoom'])]
    public FriendshipStatus $status;

    #[Groups(['read:friendshipWithRoom'])]
    public ?\DateTime $createdAt = null;

    #[Groups(['read:friendshipWithRoom'])]
    public GroupReadDTO $room;
}
