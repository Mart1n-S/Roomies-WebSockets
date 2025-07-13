<?php

namespace App\ApiResource\Group;

use App\Entity\Room;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\Metadata\Patch;
use App\Dto\Group\GroupReadDTO;
use App\Dto\Group\GroupCreateDTO;
use App\Dto\Group\GroupAddMemberDTO;
use ApiPlatform\Metadata\ApiResource;
use App\State\Group\GroupReadProvider;
use ApiPlatform\Metadata\GetCollection;
use App\State\Group\GroupCreateProcessor;
use App\Dto\Group\GroupMemberRolePatchDTO;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\Group\GroupAddMemberProcessor;
use App\State\Group\GroupMemberRolePatchProcessor;
use App\Dto\Group\GroupPrivateChatVisibilityPatchDTO;
use App\State\Group\GroupPrivateChatVisibilityPatchProcessor;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


#[ApiResource(
    shortName: 'Group Discussion',
    stateOptions: new Options(
        entityClass: Room::class,
    ),
    paginationEnabled: false,
    operations: [
        new Post(
            uriTemplate: '/groups',
            input: GroupCreateDTO::class,
            output: GroupReadDTO::class,
            processor: GroupCreateProcessor::class,
            name: 'groupCreate',
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            description: 'Créer un groupe de discussion avec un nom personnalisé et une liste d’amis (via leurs codes amis).',
            denormalizationContext: [
                'groups' => ['create:group'],
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
            ],
            normalizationContext: [
                'groups' => ['read:group']
            ],
            openapi: new Model\Operation(
                summary: 'Créer un groupe de discussion (type serveur)',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => new Model\MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'name' => [
                                        'type' => 'string',
                                        'example' => 'Groupe des Devs',
                                        'description' => 'Nom du groupe (3 à 30 caractères, lettres, chiffres et espaces autorisés)'
                                    ],
                                    'members' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'string',
                                            'example' => 'FDBC1A9674538ECC13CC',
                                            'description' => 'Code ami d’un utilisateur'
                                        ],
                                        'description' => 'Liste des codes amis (20 caractères hexadécimaux, A-F et 0-9)'
                                    ]
                                ],
                                'required' => ['name', 'members']
                            ])
                        )
                    ])
                ),
                responses: [
                    '201' => [
                        'description' => 'Groupe créé avec succès.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => [
                                            'type' => 'string',
                                            'format' => 'uuid',
                                            'example' => '8a7b8c60-1234-4e9d-abc0-8bc3e000b123'
                                        ],
                                        'name' => [
                                            'type' => 'string',
                                            'example' => 'Groupe des Devs'
                                        ],
                                        'isGroup' => [
                                            'type' => 'boolean',
                                            'example' => true
                                        ],
                                        'createdAt' => [
                                            'type' => 'string',
                                            'format' => 'date-time',
                                            'example' => '2025-06-09T18:32:00+00:00'
                                        ],
                                        'members' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'id' => [
                                                        'type' => 'string',
                                                        'format' => 'uuid',
                                                        'example' => '8a7b8c60-1234-4e9d-abc0-8bc3e000b123'
                                                    ],
                                                    'member' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'pseudo' => ['type' => 'string', 'example' => 'JeanDev'],
                                                            'avatar' => ['type' => 'string', 'example' => 'jean.png'],
                                                            'friendCode' => ['type' => 'string', 'example' => 'FDBC1A9674538ECC13CC']
                                                        ]
                                                    ],
                                                    'isVisible' => [
                                                        'type' => 'boolean',
                                                        'example' => true
                                                    ],
                                                    'role' => [
                                                        'type' => 'string',
                                                        'example' => 'owner',
                                                        'enum' => ['owner', 'admin', 'user']
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Erreur de validation (nom invalide, trop peu de membres, codes amis incorrects, etc.)'
                    ]
                ]
            )
        ),
        new Post(
            uriTemplate: '/groups/{id}/members',
            input: GroupAddMemberDTO::class,
            output: GroupReadDTO::class,
            processor: GroupAddMemberProcessor::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            denormalizationContext: [
                'groups' => ['add:groupMember'],
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
            ],
            normalizationContext: ['groups' => ['read:group']],
            openapi: new Model\Operation(
                summary: "Ajouter un membre à un groupe",
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => new Model\MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'friendCode' => [
                                        'type' => 'string',
                                        'example' => 'FDBC1A9674538ECC13CC',
                                        'description' => 'Code ami de l’utilisateur à ajouter'
                                    ]
                                ],
                                'required' => ['friendCode']
                            ])
                        )
                    ])
                ),
                responses: [
                    '201' => [
                        'description' => "Membre ajouté avec succès, retourne l'état complet du groupe",
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => [
                                            'type' => 'string',
                                            'format' => 'uuid',
                                            'example' => '8a7b8c60-1234-4e9d-abc0-8bc3e000b123'
                                        ],
                                        'name' => [
                                            'type' => 'string',
                                            'example' => 'Groupe des Devs'
                                        ],
                                        'isGroup' => [
                                            'type' => 'boolean',
                                            'example' => true
                                        ],
                                        'createdAt' => [
                                            'type' => 'string',
                                            'format' => 'date-time',
                                            'example' => '2025-06-09T18:32:00+00:00'
                                        ],
                                        'members' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'id' => [
                                                        'type' => 'string',
                                                        'format' => 'uuid',
                                                        'example' => '8a7b8c60-1234-4e9d-abc0-8bc3e000b123'
                                                    ],
                                                    'member' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'pseudo' => ['type' => 'string', 'example' => 'JeanDev'],
                                                            'avatar' => ['type' => 'string', 'example' => 'jean.png'],
                                                            'friendCode' => ['type' => 'string', 'example' => 'FDBC1A9674538ECC13CC']
                                                        ]
                                                    ],
                                                    'isVisible' => [
                                                        'type' => 'boolean',
                                                        'example' => true
                                                    ],
                                                    'role' => [
                                                        'type' => 'string',
                                                        'example' => 'owner',
                                                        'enum' => ['owner', 'admin', 'user']
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => "Erreur de validation : code invalide, non ami, déjà membre, etc."
                    ]
                ]
            )
        ),
        new Patch(
            uriTemplate: '/groups/{groupId}/members',
            input: GroupMemberRolePatchDTO::class,
            output: GroupReadDTO::class,
            processor: GroupMemberRolePatchProcessor::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            denormalizationContext: [
                'groups' => ['patch:group:member'],
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false
            ],
            normalizationContext: ['groups' => ['read:group']],
            description: 'Modifier le rôle d’un membre du groupe (seul le propriétaire peut le faire).',
            openapi: new Model\Operation(
                summary: 'Modifier le rôle d’un membre du groupe',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/merge-patch+json' => new Model\MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'memberId' => [
                                        'type' => 'string',
                                        'format' => 'uuid',
                                        'example' => '123e4567-e89b-12d3-a456-426614174000',
                                        'description' => 'Identifiant du membre dont on veut changer le rôle'
                                    ],
                                    'role' => [
                                        'type' => 'string',
                                        'enum' => ['user', 'admin'],
                                        'example' => 'admin',
                                        'description' => 'Nouveau rôle à attribuer'
                                    ]
                                ],
                                'required' => ['memberId', 'role']
                            ])
                        )
                    ])
                ),
                responses: [
                    '200' => [
                        'description' => 'Rôle modifié avec succès.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => [
                                            'type' => 'string',
                                            'format' => 'uuid',
                                            'example' => '8a7b8c60-1234-4e9d-abc0-8bc3e000b123'
                                        ],
                                        'name' => [
                                            'type' => 'string',
                                            'example' => 'Groupe des Devs'
                                        ],
                                        'isGroup' => [
                                            'type' => 'boolean',
                                            'example' => true
                                        ],
                                        'createdAt' => [
                                            'type' => 'string',
                                            'format' => 'date-time',
                                            'example' => '2025-06-09T18:32:00+00:00'
                                        ],
                                        'members' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'id' => [
                                                        'type' => 'string',
                                                        'format' => 'uuid',
                                                        'example' => '8a7b8c60-1234-4e9d-abc0-8bc3e000b123'
                                                    ],
                                                    'member' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'pseudo' => ['type' => 'string', 'example' => 'JeanDev'],
                                                            'avatar' => ['type' => 'string', 'example' => 'jean.png'],
                                                            'friendCode' => ['type' => 'string', 'example' => 'FDBC1A9674538ECC13CC']
                                                        ]
                                                    ],
                                                    'isVisible' => [
                                                        'type' => 'boolean',
                                                        'example' => true
                                                    ],
                                                    'role' => [
                                                        'type' => 'string',
                                                        'example' => 'owner',
                                                        'enum' => ['owner', 'admin', 'user']
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                ]
            )
        ),
        new GetCollection(
            uriTemplate: '/groups/private/chat',
            name: 'myRoomsGetCollection',
            provider: GroupReadProvider::class,
            output: GroupReadDTO::class,
            normalizationContext: ['groups' => ['read:group']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            description: 'Récupère toutes les discussions privées de l\'utilisateur.',
            openapi: new Model\Operation(
                summary: 'Liste des discussions privées de l’utilisateur.'
            )
        ),
        new Patch(
            uriTemplate: '/groups/private/chat/{id}/visibility',
            input: GroupPrivateChatVisibilityPatchDTO::class,
            processor: GroupPrivateChatVisibilityPatchProcessor::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            denormalizationContext: [
                'groups' => ['patch:chat:visibility'],
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false
            ],
            openapi: new Model\Operation(
                summary: 'Met à jour la visibilité d\'une discussion privée',
                description: 'Permet de cacher ou afficher une discussion privée dans la liste des conversations',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/merge-patch+json' => new Model\MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'isVisible' => [
                                        'type' => 'boolean',
                                        'example' => false,
                                        'description' => 'Nouvel état de visibilité de la conversation'
                                    ]
                                ],
                                'required' => ['isVisible']
                            ])
                        )
                    ])
                ),
                responses: [
                    '200' => [
                        'description' => 'Visibilité mise à jour avec succès',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => [
                                            'type' => 'string',
                                            'example' => 'Visibilité mise à jour'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Requête invalide (mauvais format, ID invalide)'
                    ],
                    '401' => [
                        'description' => 'Authentification requise'
                    ],
                    '403' => [
                        'description' => 'Pas autorisé à modifier cette conversation'
                    ],
                    '404' => [
                        'description' => 'Conversation introuvable'
                    ]
                ]
            )
        )
    ]
)]
final class GroupResource {}
