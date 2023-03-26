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
            self::fail('The Owner user not found. Did you load the fixtures?');
        }

        self::$userId = $user->getId() ?? 0;
    }

    public function testCreateUser(): void
    {
        static::createClient()->request('POST', '/api/users', ['json' => [
            'email' => 'foo-test-will-failed',
            'password' => 'foo-test1',
            'pseudonym' => 'Foo Test1',
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
        ]);
    }

    public function testDelete(): void
    {
        static::createClient()->request('DELETE', '/api/users/'.self::$userId);
        self::assertResponseStatusCodeSame(401);
        self::assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
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
            'hydra:totalItems' => 13,
        ]);

        self::assertHydraCollectionOnlyContainsKeysInMember(['@id', '@type', 'pseudonym', 'admin'], $response);
    }

    public function testPut(): void
    {
        static::createClient()->request('PUT', '/api/users/'.self::$userId, ['json' => [
            'email' => 'foo-test@example.org',
            'password' => 'foo-test1',
            'pseudonym' => 'Foo Test1',
        ]]);
        self::assertResponseStatusCodeSame(401);
        self::assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }
}
