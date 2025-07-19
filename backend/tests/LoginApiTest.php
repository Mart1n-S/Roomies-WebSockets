<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class LoginApiTest extends ApiTestCase
{
    /**
     * Teste le login et la présence des cookies d'auth (BEARER, refresh_token).
     */
    public function testLoginSuccess(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/login', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => 'user@user.com',
                'password' => 'password'
            ]
        ]);

        // Vérifie le status code attendu (204 No Content)
        $this->assertResponseStatusCodeSame(204);

        // Récupère tous les cookies
        $cookies = $client->getCookieJar()->all();
        $cookieNames = array_map(fn($cookie) => $cookie->getName(), $cookies);

        $this->assertContains('BEARER', $cookieNames, 'Le cookie BEARER est absent');
        $this->assertContains('refresh_token', $cookieNames, 'Le cookie refresh_token est absent');
    }

    /**
     * Teste le login avec des identifiants incorrects.
     */
    public function testLoginFails(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/login', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => 'wrong@user.com',
                'password' => 'badpassword'
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
    }
}
