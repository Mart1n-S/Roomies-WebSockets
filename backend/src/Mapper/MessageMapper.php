<?php

namespace App\Mapper;

use App\Dto\Message\MessageReadDTO;
use App\Entity\Message;
use App\Mapper\UserMapper;

class MessageMapper
{
    public function __construct(private UserMapper $userMapper) {}

    /**
     * Transforme un Message en MessageReadDTO
     *
     * @param Message $message
     * @return MessageReadDTO
     */
    public function toReadDTO(Message $message): MessageReadDTO
    {
        $dto = new MessageReadDTO();
        $dto->id = $message->getId()->toRfc4122();
        $dto->content = $message->getContent();
        $dto->createdAt = $message->getCreatedAt();
        $dto->roomId = $message->getRoom()->getId()->toRfc4122();
        $dto->type = $message->getType()->value;

        $dto->sender = $this->userMapper->toReadDto($message->getSender());

        return $dto;
    }
}
