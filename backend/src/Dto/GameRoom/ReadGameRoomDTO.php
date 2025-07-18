<?php

namespace App\Dto\GameRoom;

use App\Dto\User\UserReadDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class ReadGameRoomDTO
{
    #[Groups(['read:game_room'])]
    public string $id;

    #[Groups(['read:game_room'])]
    public string $name;

    #[Groups(['read:game_room'])]
    public string $game;

    #[Groups(['read:game_room'])]
    public UserReadDTO $creator;

    #[Groups(['read:game_room'])]
    public \DateTimeImmutable $createdAt;
}
