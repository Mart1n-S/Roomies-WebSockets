<?php

namespace App\ApiResource\User;

use App\Entity\User;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Dto\User\UserPatchDTO;
use App\Dto\User\UserCreateDTO;
use App\State\User\UserReadProvider;
use ApiPlatform\Metadata\ApiResource;
use App\State\User\UserPatchProcessor;
use App\State\User\UserCreateProcessor;
use ApiPlatform\Doctrine\Orm\State\Options;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[ApiResource(
    shortName: 'User',
    stateOptions: new Options(
        entityClass: User::class,
    ),
    paginationEnabled: false,
    operations: [
        new Post(
            uriTemplate: '/user',
            input: UserCreateDTO::class,
            processor: UserCreateProcessor::class,
            name: 'userPost',
            formats: [
                'multipart' => ['multipart/form-data'],
                'json' => ['application/json']
            ],
            security: "is_granted('PUBLIC_ACCESS')",
            description: 'Créer un nouvel utilisateur avec un avatar (multipart/form-data)',
            denormalizationContext: [
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
            ],
            openapi: new Model\Operation(
                summary: 'Inscription d’un nouvel utilisateur avec upload d’avatar',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => new Model\MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'email' => ['type' => 'string', 'example' => 'test@example.com'],
                                    'password' => ['type' => 'string', 'example' => 'MotDePasseSecure123!'],
                                    'pseudo' => ['type' => 'string', 'example' => 'PseudoCool'],
                                    'avatar' => ['type' => 'string', 'format' => 'binary']
                                ],
                                'required' => ['email', 'password', 'pseudo']
                            ])
                        )
                    ])
                ),
                responses: [
                    '201' => [
                        'description' => 'Utilisateur créé avec succès',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'Utilisateur créé avec succès.']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
        new Get(
            uriTemplate: '/user',
            provider: UserReadProvider::class,
            name: 'userGet',
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            description: 'Récupérer les informations de l’utilisateur connecté',
            openapi: new Model\Operation(
                summary: 'Récupération des infos de l’utilisateur connecté',
                responses: [
                    '200' => [
                        'description' => 'Données de l’utilisateur connecté',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'email' => ['type' => 'string', 'example' => 'test@example.com'],
                                        'pseudo' => ['type' => 'string', 'example' => 'PseudoCool'],
                                        'avatar' => [
                                            'type' => 'string',
                                            'example' => 'https://localhost:8000/media/photo.jpg'
                                        ],
                                        'roles' => [
                                            'type' => 'array',
                                            'items' => ['type' => 'string'],
                                            'example' => ['ROLE_USER']
                                        ],
                                        'friendCode' => [
                                            'type' => 'string',
                                            'example' => 'A1B2C3D4E5F6A7B8C9D0'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '403' => [
                        'description' => 'Non authentifié',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => [
                                            'type' => 'string',
                                            'example' => 'Vous devez être connecté pour accéder à cette ressource.'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
        // Pourquoi un POST ici ?
        /*
        * ⚠️ Swagger (et donc l’interface API Platform) ne gère pas correctement les requêtes PATCH
        * avec le format multipart/form-data (utile pour l’envoi de fichiers).
        *
        * Pour contourner cette limitation, on utilise un POST spécifique qui agit en réalité comme un PATCH.
        * Voir documentation officielle : https://api-platform.com/docs/symfony/file-upload/#using-the-custom-controller-strategy
        */
        new Post(
            uriTemplate: '/user/edit',
            input: UserPatchDTO::class,
            processor: UserPatchProcessor::class,
            name: 'userAvatarEdit',
            formats: [
                'multipart' => ['multipart/form-data'],
                'json' => ['application/json']
            ],
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            description: 'Mise à jour du pseudo, mot de passe ou avatar (upload multipart). Contourne la limite Swagger sur PATCH multipart.',
            openapi: new Model\Operation(
                summary: 'Modifier pseudo, mot de passe ou avatar via POST (multipart)',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => new Model\MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'pseudo' => ['type' => 'string', 'example' => 'NewPseudoCool'],
                                    'avatar' => ['type' => 'string', 'format' => 'binary'],
                                    'currentPassword' => ['type' => 'string', 'example' => 'AncienMotDePasse123!'],
                                    'newPassword' => ['type' => 'string', 'example' => 'NouveauMotDePasseUltraSecure456@']
                                ]
                            ])
                        )
                    ])
                ),
                responses: [
                    '200' => [
                        'description' => 'Utilisateur mis à jour avec succès',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'email' => ['type' => 'string', 'example' => 'newemail@example.com'],
                                        'pseudo' => ['type' => 'string', 'example' => 'NewPseudoCool'],
                                        'avatar' => ['type' => 'string', 'example' => 'http://localhost:8000/media/newavatar.jpg'],
                                        'friendCode' => [
                                            'type' => 'string',
                                            'example' => 'A1B2C3D4E5F6A7B8C9D0'
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Erreur de validation ou mot de passe incorrect',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'Le mot de passe actuel est requis pour changer le mot de passe.']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
    ]
)]
class UserResource {}
