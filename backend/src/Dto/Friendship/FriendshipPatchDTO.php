<?php

namespace App\Dto\Friendship;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

final class FriendshipPatchDTO
{
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['accepter', 'refuser'], message: "Action non valide. Utilisez 'accepter' ou 'refuser'.")]
    #[Groups(['patch:friendship'])]
    public string $action;
}
