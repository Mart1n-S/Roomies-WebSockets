<?php

namespace App\Dto\Group;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

class GroupPrivateChatVisibilityPatchDTO
{
    #[Assert\Type('bool', message: 'La visibilité doit vrai ou faux.')]
    #[Assert\NotNull(message: 'La visibilité doit être spécifiée (true/false).')]
    #[Groups(['patch:chat:visibility'])]
    public bool $isVisible;
}
