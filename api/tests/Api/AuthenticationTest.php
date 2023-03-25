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
use Symfony\Contracts\HttpClient\ResponseInterface;

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

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected static function assertHydraCollectionContainsKeysInMember(array $keys, ResponseInterface $response): void
    {
        self::assertJsonContains(['@type' => 'hydra:Collection']);

        $hydraMembers = $response->toArray()['hydra:member'];
        $firstMember = array_shift($hydraMembers);
        if (null === $firstMember) {
            self::fail('The collection is empty! Did you forget to load initial data fixtures? Try to launch `php bin/console doctrine:fixtures:load --env=test`');
        }

        foreach ($keys as $key) {
            self::assertArrayHasKey($key, $firstMember);
        }
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected static function assertHydraCollectionNotContainsKeysInMember(array $keys, ResponseInterface $response): void
    {
        self::assertJsonContains(['@type' => 'hydra:Collection']);

        $hydraMembers = $response->toArray()['hydra:member'];
        $firstMember = array_shift($hydraMembers);

        foreach ($keys as $key) {
            self::assertArrayNotHasKey($key, $firstMember);
        }
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
}
