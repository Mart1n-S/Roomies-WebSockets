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
        // TODO: A supprimer à la fin du projet pour clean le code car on passe par websocket
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
