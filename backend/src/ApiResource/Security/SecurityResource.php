<?php

namespace App\ApiResource\Security;

use ApiPlatform\Metadata\Get;
use App\Dto\User\UserReadDTO;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\Metadata\ApiResource;
use App\Dto\User\PushSubscriptionDTO;
use App\Controller\SecurityController;
use App\Dto\Websocket\WebSocketTokenRead;
use App\Controller\RegistrationController;
use App\State\User\UserPatchNotificationProcessor;
use App\State\WebSocket\Security\WebSocketTokenProvider;


#[ApiResource(
    operations: [
        // <-------------------------------------- Partie Login --------------------------------------->
        new Post(
            uriTemplate: '/token/refresh',
            name: 'refreshToken',
            security: "is_granted('PUBLIC_ACCESS')",
            description: 'Rafraîchir le token utilisateur à partir du cookie sécurisé contenant le refresh token.',
            openapi: new Model\Operation(
                tags: ['Security'],
                summary: 'Rafraîchissement du token utilisateur avec le refresh token en cookie sécurisé',
                responses: [
                    '200' => [
                        'description' => 'Un nouveau JWT a été généré grâce au refresh token.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'token' => ['type' => 'string', 'example' => 'new.jwt.token'],
                                        'refresh_token' => ['type' => 'string', 'example' => 'new.refresh.token']
                                    ],
                                    'required' => ['token', 'refresh_token']
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => 'Refresh token manquant, invalide ou expiré.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'Le refresh token est invalide ou expiré.']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
        new Post(
            uriTemplate: '/logout',
            name: 'logout',
            security: "is_granted('PUBLIC_ACCESS')",
            openapi: new Model\Operation(
                summary: 'Déconnexion utilisateur',
                description: 'Déconnecte l’utilisateur en supprimant le refresh token du cookie et de la base de données.',
                tags: ['Security'],
                responses: [
                    '204' => [
                        'description' => 'Déconnexion réussie, aucun contenu retourné.',
                    ],
                    '401' => [
                        'description' => 'L’utilisateur n’est pas authentifié ou le token est invalide.',
                    ],
                ]
            )
        ),
        // <-------------------------------------- Partie réinitialisation du mot de passe --------------------------------------->
        // Route pour la demande de réinitialisation de mot de passe
        new Post(
            uriTemplate: '/request-password-reset',
            controller: SecurityController::class . '::requestPasswordReset',
            name: 'requestPasswordReset',
            security: "is_granted('PUBLIC_ACCESS')",
            description: 'Demander la réinitialisation de mot de passe',
            openapi: new Model\Operation(
                tags: ['Security'],
                summary: 'Demander une réinitialisation de mot de passe',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => new Model\MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'email' => ['type' => 'string', 'example' => 'user@example.com'],
                                ],
                                'required' => ['email']
                            ])
                        )
                    ])
                ),
                responses: [
                    '200' => [
                        'description' => 'Un email a été envoyé pour la réinitialisation du mot de passe',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'Si un compte existe, un email de réinitialisation a été envoyé.']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Erreur dans la demande de réinitialisation',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'L\'email est requis.']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
        // Route pour la réinitialisation du mot de passe avec un token
        new Post(
            uriTemplate: '/reset-password',
            controller: SecurityController::class . '::resetPassword',
            name: 'resetPassword',
            security: "is_granted('PUBLIC_ACCESS')",
            description: 'Réinitialiser le mot de passe avec un token',
            openapi: new Model\Operation(
                tags: ['Security'],
                summary: 'Réinitialisation de mot de passe',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => new Model\MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'email' => ['type' => 'string', 'example' => 'user@example.com'],
                                    'token' => ['type' => 'string', 'example' => 'abc123resetToken'],
                                    'password' => ['type' => 'string', 'example' => 'NouveauMotDePasse!2025'],
                                ],
                                'required' => ['email', 'token', 'password']
                            ])
                        )
                    ])
                ),
                responses: [
                    '200' => [
                        'description' => 'Mot de passe réinitialisé avec succès',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'Mot de passe réinitialisé avec succès.']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Erreur de validation ou token invalide',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'Le token n\'est pas valide.']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
        // <-------------------------------------- Partie vérification de l'email --------------------------------------->
        // Route pour la vérification de l'email de l'utilisateur
        new Post(
            uriTemplate: '/verify-email',
            name: 'verify-email',
            controller: RegistrationController::class . '::verifyUserEmail',
            security: "is_granted('PUBLIC_ACCESS')",
            description: 'Vérification de l\'email de l\'utilisateur',
            openapi: new Model\Operation(
                tags: ['Security'],
                summary: 'Vérification de l\'email de l\'utilisateur',
                description: 'Cette opération permet de vérifier l\'email de l\'utilisateur. ' .
                    '<strong>Important :</strong> Avant d\'envoyer la requête, veuillez <em>décoder les paramètres de l\'URL</em>. ' .
                    'Pour ce faire, vous pouvez utiliser un outil en ligne tel que ' .
                    '<a href="https://www.urldecoder.org/" target="_blank" style="color: #007bff; text-decoration: underline;">ce site</a>.',

                parameters: [
                    [
                        'name' => 'expires',
                        'in' => 'query',
                        'required' => true,
                        'description' => 'Date d\'expiration du lien',
                        'schema' => [
                            'type' => 'string',
                            'example' => '1744055273'
                        ]
                    ],
                    [
                        'name' => 'id',
                        'in' => 'query',
                        'required' => true,
                        'description' => 'ID de l\'utilisateur',
                        'schema' => [
                            'type' => 'string',
                            'example' => '01961194-9add-73ef-951b-0a1744a1996a'
                        ]
                    ],
                    [
                        'name' => 'signature',
                        'in' => 'query',
                        'required' => true,
                        'description' => 'Signature pour vérifier l\'email',
                        'schema' => [
                            'type' => 'string',
                            'example' => 'iGJ%2FtsbA4x7LxEtMzHr1dbBMtJIPibNU9I%2B4kcdLVrI%3D'
                        ]
                    ],
                    [
                        'name' => 'token',
                        'in' => 'query',
                        'required' => true,
                        'description' => 'Token de vérification unique',
                        'schema' => [
                            'type' => 'string',
                            'example' => 'w1BkprEpumwJ6cBuY8vUQNgOlFZ18MzA0ppSVqnFyoo%3D'
                        ]
                    ]
                ],
                responses: [
                    '200' => [
                        'description' => 'Email vérifié avec succès',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'Email vérifié avec succès.']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Erreur de vérification de l\'email',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'Token ou signature invalide.']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
        new Post(
            uriTemplate: '/request-new-confirmation-email',
            name: 'request-new-confirmation-email',
            controller: RegistrationController::class . '::requestNewConfirmationEmail',
            security: "is_granted('PUBLIC_ACCESS')",
            description: 'Demander un nouvel email de confirmation pour un utilisateur non confirmé ou dont le lien de confirmation a expiré',
            openapi: new Model\Operation(
                tags: ['Security'],
                summary: 'Demander un nouvel email de confirmation',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => new Model\MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'email' => ['type' => 'string', 'example' => 'user@example.com']
                                ],
                                'required' => ['email']
                            ])
                        )
                    ])
                ),
                responses: [
                    '200' => [
                        'description' => 'Si un compte existe, un nouvel email de confirmation sera envoyé.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'Si un compte existe, un nouvel email de confirmation sera envoyé.']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Erreur si l\'email est déjà confirmé ou si l\'email est invalide',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'L\'email a déjà été confirmé.']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '500' => [
                        'description' => 'Erreur lors de l\'envoi de l\'email',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'Échec de l\'envoi de l\'email de confirmation. Veuillez réessayer ultérieurement.']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
        new Get(
            uriTemplate: '/ws/token',
            name: 'get_websocket_token',
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            output: WebSocketTokenRead::class,
            provider: WebSocketTokenProvider::class,
            openapi: new Model\Operation(
                summary: 'Obtenir un token WebSocket temporaire',
                description: 'Retourne un JWT de courte durée (2 minutes) pour établir une connexion WebSocket sécurisée. Le JWT est signé côté serveur et contient l\'ID et l\'identifiant de l\'utilisateur.',
                tags: ['Security'],
                responses: [
                    '200' => [
                        'description' => 'JWT temporaire pour WebSocket',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'token' => ['type' => 'string', 'example' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...'],
                                        'expires_at' => ['type' => 'integer', 'example' => 1744055273]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => 'Non authentifié',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'Unauthorized']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
        new Post(
            uriTemplate: '/subscribe',
            name: 'subscribe_notifications',
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            input: PushSubscriptionDTO::class,
            output: UserReadDTO::class,
            processor: UserPatchNotificationProcessor::class,
            openapi: new Model\Operation(
                summary: "S'abonner aux notifications",
                description: "Permet à l'utilisateur de s'abonner aux notifications push.",
                tags: ['Security']
            )
        ),


    ]
)]
class SecurityResource {}
