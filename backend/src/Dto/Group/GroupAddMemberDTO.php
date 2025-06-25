<?php

namespace App\Dto\Group;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

class GroupAddMemberDTO
{
    #[Assert\NotBlank(
        message: 'Le code ami ne peut pas être vide.'
    )]
    #[Assert\Length(
        exactly: 20,
        exactMessage: 'Le code ami doit contenir exactement 20 caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-F0-9]{20}$/',
        message: 'Le code ami doit contenir uniquement des lettres majuscules (A-F) et des chiffres (0-9), sur 20 caractères.'
    )]
    #[Groups(['add:groupMember'])]
    public string $friendCode;
}
