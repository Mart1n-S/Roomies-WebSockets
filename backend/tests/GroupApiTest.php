<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class CreateGroupApiTest extends ApiTestCase
{
    /**
     * Ce test simule l’authentification d’un utilisateur via /api/login,
     * récupère le cookie BEARER, puis effectue une requête POST sur /api/groups
     * pour créer un groupe avec des membres.
     */
    public function testCreateGroupWithAuth(): void
    {
        // Données de login valides (assure-toi que ce user existe en base de test)
        $email = 'user@user.com';
        $password = 'password';

        // 1. Authentification sur /api/login
        $client = static::createClient();
        $client->request('POST', '/api/login', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'email' => $email,
                'password' => $password,
            ]
        ]);

        // 2. Vérifie que le login renvoie bien 204 (no content)
        $this->assertResponseStatusCodeSame(204);

        // 3. Récupère le cookie BEARER
        $cookieJar = $client->getCookieJar();
        $bearerCookie = $cookieJar->get('BEARER', '/', null); // null pour le domaine
        $this->assertNotNull($bearerCookie, 'Le cookie BEARER est attendu.');
        $bearerToken = $bearerCookie->getValue();

        // 4. Effectue une requête authentifiée pour créer un groupe
        $response = $client->request('POST', '/api/groups', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                // L’authentification est gérée via le cookie BEARER automatiquement,
                // pas besoin d’ajouter un header Authorization ici.
            ],
            'json' => [
                'name' => 'Pipou',
                'members' => [
                    '47A350833EA98050C4E9',
                    '581193634B58880F5ECE'
                ]
            ]
        ]);

        // 5. Vérifie la réponse (code 201 attendu à la création)
        $this->assertResponseStatusCodeSame(201);

        // 6. Vérifie le contenu de la réponse (optionnel)
        $data = $response->toArray();
        $this->assertArrayHasKey('name', $data);
        $this->assertEquals('Pipou', $data['name']);
        $this->assertArrayHasKey('members', $data);
        $this->assertCount(3, $data['members'], 'Le groupe doit contenir 3 membres (créateur + 2 membres)');
    }

    /**
     * Ce test vérifie qu'une tentative de création d’un groupe
     * avec moins de 2 membres (hors créateur) échoue avec une 422.
     */
    public function testCreateGroupFailsWithNotEnoughMembers(): void
    {
        $email = 'user@user.com';
        $password = 'password';

        $client = static::createClient();
        $client->request('POST', '/api/login', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'email' => $email,
                'password' => $password,
            ]
        ]);
        $this->assertResponseStatusCodeSame(204);

        // Un seul membre fourni
        $response = $client->request('POST', '/api/groups', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'name' => 'SoloGroup',
                'members' => [
                    '581193634B58880F5ECE'
                ]
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);

        $data = $response->toArray(false);
        $this->assertArrayHasKey('violations', $data);
        $this->assertEquals(
            "Un groupe doit contenir au moins 2 membres en plus de vous-même.",
            $data['violations'][0]['message']
        );
        $this->assertEquals('members', $data['violations'][0]['propertyPath']);
    }
}
