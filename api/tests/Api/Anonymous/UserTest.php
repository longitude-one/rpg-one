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

namespace App\Tests\Api\Anonymous;

use App\Entity\User;
use App\Tests\Api\MyApiTest;

class UserTest extends MyApiTest
{
    private static int $userId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $user = self::getEntityManager()->getRepository(User::class)->findOneByPseudonym('DefaultUser');
        if (null === $user) {
            self::fail('The DefaultUser user not found. Did you load the fixtures?');
        }

        self::$userId = $user->getId() ?? 0;
    }

    public function testCreateUser(): void
    {
        $response = static::createClient()->request('POST', '/api/users', ['json' => [
            'email' => 'foo-test-42@example.org',
            'plainPassword' => 'foo-test42',
            'pseudonym' => 'Foo Test42',
        ]]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'https://schema.org/Person',
        ]);
        self::assertOnlyContainsKeys(['@context', '@id', '@type', 'pseudonym', /* 'email', */ 'admin'], $response);
    }

    public function testDelete(): void
    {
        static::createClient()->request('DELETE', '/api/users/'.self::$userId);
        self::assetJwtTokenNotFound();
    }

    public function testGet(): void
    {
        $response = static::createClient()->request('GET', '/api/users');
        $url = $response->toArray()['hydra:member'][0]['@id'];

        $response = static::createClient()->request('GET', $url);
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => $url,
            '@type' => 'https://schema.org/Person',
        ]);

        self::assertOnlyContainsKeys(['@context', '@id', '@type', 'pseudonym', 'admin'], $response);
    }

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/users');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
        ]);

        self::assertHydraCollectionOnlyContainsKeysInMember(['@id', '@type', 'pseudonym', 'admin'], $response);
    }

    public function testPatch(): void
    {
        static::createClient()->request('PATCH', '/api/users/'.self::$userId, ['headers' => [
            'Content-type' => 'application/merge-patch+json',
        ]]);

        self::assetJwtTokenNotFound();
    }

    public function testPut(): void
    {
        static::createClient()->request('PUT', '/api/users/'.self::$userId, ['json' => [
            'email' => 'foo-test@example.org',
            'password' => 'foo-test1',
            'pseudonym' => 'Foo Test1',
        ]]);
        self::assetJwtTokenNotFound();
    }
}
