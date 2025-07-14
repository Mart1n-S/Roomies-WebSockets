<?php

namespace App\ApiResource\Friendship;

use App\Entity\Friendship;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\ApiFilter\FriendshipFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\Friendship\FriendshipReadDTO;
use App\Dto\Friendship\FriendshipPatchDTO;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\Dto\Friendship\FriendshipCreateDTO;
use App\State\Friendship\FriendshipFilterProvider;
use App\State\Friendship\FriendshipPatchProcessor;
use App\State\Friendship\FriendshipCreateProcessor;
use App\State\Friendship\FriendshipDeleteProcessor;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[ApiResource(
    shortName: 'Friendship',
    stateOptions: new Options(
        entityClass: Friendship::class,
    ),
    paginationEnabled: false,
    operations: [
        new GetCollection(
            uriTemplate: '/friendships',
            name: 'friendshipsFilteredGetCollection',
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            provider: FriendshipFilterProvider::class,
            filters: [FriendshipFilter::class],
            output: FriendshipReadDTO::class,
            normalizationContext: ['groups' => ['read:friendship']],
            description: "Récupère la liste des relations d’amis filtrées (amis, demandes reçues ou envoyées).",
            openapi: new Model\Operation(
                summary: 'Liste des relations d’amis filtrées (amis, demandes reçues/envoyées).',
                description: 'Ajoutez `?filter=received` pour les demandes reçues, `?filter=sent` pour les demandes envoyées. Sans filtre, la route renvoie par défaut la liste des amis confirmés de l’utilisateur connecté.',
            )
        ),
        /**
         * TODO: A supprimer à la fin du projet pour clean le code
         * Se fait via Websocket, pas d'API REST
         */
        // new Post(
        //     uriTemplate: '/friendships',
        //     input: FriendshipCreateDTO::class,
        //     output: FriendshipReadDTO::class,
        //     processor: FriendshipCreateProcessor::class,
        //     name: 'friendshipPost',
        //     security: "is_granted('IS_AUTHENTICATED_FULLY')",
        //     description: "Envoyer une demande d’ami via un code ami (20 caractères hexadécimaux, en majuscules).",
        //     denormalizationContext: [
        //         'groups' => ['create:friendship'],
        //         AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
        //     ],
        //     normalizationContext: [
        //         'groups' => ['read:friendship']
        //     ],
        //     openapi: new Model\Operation(
        //         summary: 'Envoyer une demande d’ami via un code ami',
        //         requestBody: new Model\RequestBody(
        //             content: new \ArrayObject([
        //                 'application/json' => new Model\MediaType(
        //                     schema: new \ArrayObject([
        //                         'type' => 'object',
        //                         'properties' => [
        //                             'friendCode' => [
        //                                 'type' => 'string',
        //                                 'example' => 'A1B2C3D4E5F6A7B8C9D0',
        //                                 'description' => 'Le code ami de la personne à ajouter (exactement 20 caractères, A-F, 0-9).'
        //                             ]
        //                         ],
        //                         'required' => ['friendCode']
        //                     ])
        //                 )
        //             ])
        //         ),
        //         responses: [
        //             '201' => [
        //                 'description' => 'Demande d’ami créée avec succès.',
        //                 'content' => [
        //                     'application/json' => [
        //                         'schema' => [
        //                             'type' => 'object',
        //                             'properties' => [
        //                                 'id' => [
        //                                     'type' => 'string',
        //                                     'format' => 'uuid',
        //                                     'example' => 'b1e10d7f-82df-4cde-86dc-6cd7ae9cfa1c'
        //                                 ],
        //                                 'applicant' => [
        //                                     'type' => 'object',
        //                                     'properties' => [
        //                                         'pseudo' => ['type' => 'string', 'example' => 'JeanDu93'],
        //                                         'avatar' => ['type' => 'string', 'example' => 'jean.png'],
        //                                         'friendCode' => ['type' => 'string', 'example' => 'A1B2C3D4E5F6A7B8C9D0']
        //                                     ]
        //                                 ],
        //                                 'recipient' => [
        //                                     'type' => 'object',
        //                                     'properties' => [
        //                                         'pseudo' => ['type' => 'string', 'example' => 'Emma123'],
        //                                         'avatar' => ['type' => 'string', 'example' => 'emma.png'],
        //                                         'friendCode' => ['type' => 'string', 'example' => 'D0C9B8A7F6E5D4C3B2A1']
        //                                     ]
        //                                 ],
        //                                 'status' => [
        //                                     'type' => 'string',
        //                                     'enum' => ['pending', 'accepted', 'refused'],
        //                                     'example' => 'pending'
        //                                 ],
        //                                 'createdAt' => [
        //                                     'type' => 'string',
        //                                     'format' => 'date-time',
        //                                     'example' => '2025-05-22T13:00:00+00:00'
        //                                 ]
        //                             ]
        //                         ]
        //                     ]
        //                 ]
        //             ],
        //             '400' => [
        //                 'description' => 'Erreur de validation (code inexistant, déjà ami, demande existante, etc.)'
        //             ]
        //         ]
        //     )
        // ),
        new Patch(
            uriTemplate: '/friendships/{id}',
            input: FriendshipPatchDTO::class,
            processor: FriendshipPatchProcessor::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            denormalizationContext: [
                'groups' => ['patch:friendship'],
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false
            ],
            normalizationContext: ['groups' => ['read:friendshipWithRoom']],
            description: "Accepter ou refuser une demande d’ami. Action attendue : 'accepter' ou 'refuser'.",
            openapi: new Model\Operation(
                summary: "Répondre à une demande d’ami",
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/merge-patch+json' => new Model\MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'action' => [
                                        'type' => 'string',
                                        'enum' => ['accepter', 'refuser'],
                                        'example' => 'accepter',
                                        'description' => "L'action à effectuer : 'accepter' ou 'refuser'"
                                    ]
                                ],
                                'required' => ['action']
                            ])
                        )
                    ])
                ),
                responses: [
                    '200' => [
                        'description' => "Réponse suite à une action sur une demande d'ami.",
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'oneOf' => [
                                        [ // Réponse si la demande est acceptée
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'string', 'format' => 'uuid'],
                                                'applicant' => ['type' => 'object'],
                                                'recipient' => ['type' => 'object'],
                                                'status' => ['type' => 'string', 'example' => 'friend'],
                                                'createdAt' => ['type' => 'string', 'format' => 'date-time']
                                            ]
                                        ],
                                        [ // Réponse si la demande est refusée
                                            'type' => 'object',
                                            'properties' => [
                                                'message' => ['type' => 'string', 'example' => 'Demande refusée.']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => "Requête invalide (ex. : action manquante ou invalide)"
                    ],
                    '403' => [
                        'description' => "L'utilisateur ne peut pas modifier cette demande."
                    ]
                ]
            )
        ),
        new Delete(
            uriTemplate: '/friendships/{id}',
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            processor: FriendshipDeleteProcessor::class,
            openapi: new Model\Operation(
                summary: "Supprimer un ami",
                description: "Supprime la relation d’amitié et les données associées (ex : messages échangés).",
                responses: [
                    '204' => ['description' => "Amitié supprimée avec succès"],
                    '403' => ['description' => "Accès refusé"],
                    '404' => ['description' => "Amitié non trouvée"]
                ]
            )
        )
    ]
)]
class FriendshipResource {}
