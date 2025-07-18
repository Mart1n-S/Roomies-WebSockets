<?php

namespace App\Dto\GameRoom;

use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\Game;
use Symfony\Component\Serializer\Annotation\Groups;

class CreateGameRoomDTO
{
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_]{2,20}$/',
        message: 'Le nom doit comporter entre 2 et 20 caractères et ne peut contenir que des lettres, des chiffres et des underscores (_).'
    )]
    #[Groups(['create:game_room'])]
    public string $name;

    #[Assert\NotBlank(message: 'Le jeu est obligatoire.')]
    #[Groups(['create:game_room'])]
    public Game $game;
}
