<?php

namespace App\Dto\Message;

use App\Dto\User\UserReadDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class MessageReadDTO
{
    #[Groups(['read:message'])]
    public string $id;

    #[Groups(['read:message'])]
    public ?string $content = null;

    #[Groups(['read:message'])]
    public ?\DateTimeImmutable $createdAt;

    #[Groups(['read:message'])]
    public string $roomId;

    #[Groups(['read:message'])]
    public UserReadDTO $sender;

    #[Groups(['read:message'])]
    public string $type;
}
