<?php

/**
 * This file is part of the RPG-One Project
 *
 * PHP 8.2 | Symfony 6.2+
 *
 * Copyright LongitudeOne - Alexandre Tranchant
 * Copyright 2023 - 2023
 *
 */

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\Client;

abstract class AuthenticationTest extends MyApiTest
{
    private static string $token = '';

    protected Client $client;

    public function setUp(): void
    {
        if (empty(self::$token)) {
            self::fail('Token is not set. Did you forget to overload setUp ?');
        }

        parent::setUp();
        $this->client = static::createClient();
        $this->client->setDefaultOptions([
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'accept' => 'application/ld+json',
                'Authorization' => 'Bearer '.self::$token,
            ],
        ]);
    }

    protected static function getUserToken(): void
    {
        static::setToken('user');
    }

    protected static function setToken(string $role): void
    {
        $response = static::createClient()->request('POST', '/api/auth', [
            'headers' => [
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
            ],
            'json' => [
                'email' => strtolower($role).'@example.org',
                'password' => 'password',
            ],
        ]);

        self::assertResponseStatusCodeSame(200);

        self::$token = $response->toArray()['token'];
    }

    protected function setModePatch(): void
    {
        $this->client->setDefaultOptions([
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
                'accept' => 'application/ld+json',
                'Authorization' => 'Bearer '.self::$token,
            ],
        ]);
    }

    protected function unsetModePatch(): void
    {
        $this->client->setDefaultOptions([
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'accept' => 'application/ld+json',
                'Authorization' => 'Bearer '.self::$token,
            ],
        ]);
    }
}
