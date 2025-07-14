<?php

namespace App\ApiResource\Message;

use App\Entity\Message;
use ApiPlatform\OpenApi\Model;
use App\Dto\Message\MessageReadDTO;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\Message\MessageReadProvider;
use ApiPlatform\Doctrine\Orm\State\Options;

#[ApiResource(
    shortName: 'Messages',
    stateOptions: new Options(
        entityClass: Message::class
    ),
    operations: [
        new GetCollection(
            uriTemplate: '/messages',
            normalizationContext: ['groups' => ['read:message']],
            paginationEnabled: true,
            paginationItemsPerPage: 40,
            provider: MessageReadProvider::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            output: MessageReadDTO::class,
            openapi: new Model\Operation(
                summary: 'Récupère les messages d’une room privée (avec pagination)',
                parameters: [
                    new Model\Parameter(
                        name: 'roomId',
                        in: 'query',
                        required: true,
                        description: 'L’identifiant de la room dont on veut les messages',
                        schema: ['type' => 'string', 'format' => 'uuid']
                    )
                ]
            )
        )
    ]
)]
final class MessageResource {}
