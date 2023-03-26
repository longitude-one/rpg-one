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

namespace App\Tests\Api\Owner;

use App\Entity\User;
use App\Tests\Api\AuthenticationTest;

class UserTest extends AuthenticationTest
{
    private static int $userId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $user = self::getEntityManager()->getRepository(User::class)->findOneByPseudonym('The Owner');
        if (null === $user) {
            self::fail('The Owner user not found. Did you load the fixtures?');
        }

        self::$userId = $user->getId() ?? 0;
    }

    public function setUp(): void
    {
        self::setToken('owner');
        parent::setUp();
    }

    public function testGet(): void
    {
        $response = $this->client->request('GET', '/api/users/'.self::$userId);
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users/'.self::$userId,
            '@type' => 'https://schema.org/Person',
        ]);

        self::assertOnlyContainsKeys(['@context', '@id', '@type', 'email', 'pseudonym', 'admin'], $response);

        $response = $this->client->request('GET', '/api/users/'.(self::$userId - 1));
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users/'.(self::$userId - 1),
            '@type' => 'https://schema.org/Person',
        ]);

        self::assertOnlyContainsKeys(['@context', '@id', '@type', 'pseudonym', 'admin'], $response);
    }

//    public function testGetCollection(): void
//    {
//        $response = static::createClient()->request('GET', '/api/users');
//        self::assertResponseIsSuccessful();
//        self::assertJsonContains([
//            '@context' => '/api/contexts/User',
//            '@id' => '/api/users',
//            '@type' => 'hydra:Collection',
//            'hydra:totalItems' => 13,
//        ]);
//
//        self::assertHydraCollectionOnlyContainsKeysInMember(['@id', '@type', 'pseudonym', 'admin'], $response);
//    }
}
