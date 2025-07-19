<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

/**
 * Classe de tests fonctionnels pour l'API d'inscription (/api/user).
 * On vérifie à la fois le cas d'inscription réussie et les erreurs de validation.
 */
class RegisterApiTest extends ApiTestCase
{
    /**
     * Teste le scénario d'inscription d'un nouvel utilisateur avec des données valides.
     *
     * Ce test vérifie que :
     * - l'endpoint /api/user accepte bien les données au format JSON,
     * - l'utilisateur est créé avec succès (statut HTTP 201),
     * - le message de succès retourné correspond à l'attendu.
     */
    public function testRegisterSuccess(): void
    {
        // On génère des valeurs uniques pour éviter les conflits en base
        $email = 'newuser' . uniqid() . '@example.com';
        $pseudo = 'User' . uniqid();
        $password = 'MotDePasseUltraSecure123!';

        // Requête POST vers l'API avec le bon Content-Type
        $response = static::createClient()->request('POST', '/api/user', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'email' => $email,
                'password' => $password,
                'pseudo' => $pseudo,
            ]
        ]);

        // Vérifie le code retour HTTP 201 (Created)
        $this->assertResponseStatusCodeSame(201);

        // Vérifie que le message de succès est bien retourné
        $this->assertJsonContains([
            'message' => 'Utilisateur créé avec succès.'
        ]);
    }

    /**
     * Teste le cas où l'inscription échoue à cause de données invalides.
     *
     * Ce test vérifie que :
     * - l'API retourne une erreur HTTP 422 pour données non valides,
     * - la structure de réponse contient bien le champ "violations",
     * - chaque champ en erreur retourne le bon message de validation.
     */
    public function testRegisterValidationFails(): void
    {
        // Données volontairement invalides pour déclencher des erreurs de validation
        $invalidData = [
            'email' => 'not-an-email',      // format invalide
            'password' => 'short',          // trop court
            'pseudo' => 'X'                 // trop court
        ];

        // Envoi de la requête POST
        $response = static::createClient()->request('POST', '/api/user', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => $invalidData
        ]);

        // Vérifie que le serveur retourne bien une erreur 422
        $this->assertResponseStatusCodeSame(422);

        // On récupère le contenu JSON de la réponse
        $responseData = $response->toArray(false);

        // On vérifie la présence du tableau "violations"
        $this->assertArrayHasKey('violations', $responseData);

        // On extrait tous les chemins de propriété et messages d'erreur
        $violations = $responseData['violations'];
        $propertyPaths = array_column($violations, 'propertyPath');
        $messages = array_column($violations, 'message');

        // On vérifie que chaque champ problématique apparaît dans la réponse
        $this->assertContains('email', $propertyPaths);
        $this->assertContains('password', $propertyPaths);
        $this->assertContains('pseudo', $propertyPaths);

        // On définit les messages attendus pour chaque champ
        $expectedMessages = [
            'email' => 'L\'adresse email n\'est pas valide.',
            'password' => [
                'Le mot de passe doit comporter au moins 16 caractères.',
                'Le mot de passe doit comporter au moins 16 caractères et contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial.'
            ],
            'pseudo' => 'Le pseudo doit comporter entre 2 et 20 caractères et ne peut contenir que des lettres, des chiffres et des underscores (_).'
        ];

        // Pour chaque champ, on vérifie la présence des messages attendus
        foreach ($expectedMessages as $field => $expected) {
            $fieldMessages = [];
            foreach ($violations as $violation) {
                if ($violation['propertyPath'] === $field) {
                    $fieldMessages[] = $violation['message'];
                }
            }

            if (is_array($expected)) {
                // Si plusieurs messages possibles pour un même champ
                $found = false;
                foreach ($expected as $msg) {
                    if (in_array($msg, $fieldMessages, true)) {
                        $found = true;
                        break;
                    }
                }
                $this->assertTrue($found, sprintf('Aucun message attendu trouvé pour %s parmi %s', $field, json_encode($expected)));
            } else {
                $this->assertContains($expected, $fieldMessages, sprintf('Message attendu non trouvé pour %s', $field));
            }
        }
    }
}
