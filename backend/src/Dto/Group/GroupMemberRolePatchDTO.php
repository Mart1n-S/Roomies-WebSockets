<?php

namespace App\Dto\Group;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class GroupMemberRolePatchDTO
{
    #[Assert\NotBlank(message: 'Merci de préciser l\'identifiant de l\'utilisateur.')]
    #[Assert\Uuid(message: 'L\'identifiant de l\'utilisateur doit être un UUID valide.')]
    #[Groups(['patch:group:member'])]
    public Uuid $memberId;

    #[Assert\NotBlank(message: 'Merci de préciser un rôle.')]
    #[Assert\Choice(choices: ['user', 'admin'], message: 'Le rôle doit être "user" ou "admin".')]
    #[Groups(['patch:group:member'])]
    public string $role;
}
