<?php

namespace App\ApiFilter;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\FilterInterface;

#[ApiFilter(FriendshipFilter::class)]
class FriendshipFilter implements FilterInterface
{
    public function getDescription(string $resourceClass): array
    {
        return [
            'filter' => [
                'property' => 'filter',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Filtrer les relations : "friends", "sent", "received"',
                    'name' => 'filter',
                    'type' => 'string',
                ],
            ],
        ];
    }
}
