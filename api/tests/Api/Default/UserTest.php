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

namespace App\Tests\Api\Default;

use App\Entity\User;
use App\Tests\Api\AuthenticationTest;

class UserTest extends AuthenticationTest
{
    private static int $otherUser;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $userRepository = self::getEntityManager()->getRepository(User::class);

        $otherUser = $userRepository->findOneByPseudonym('OtherUser');
        if (null === $otherUser) {
            self::fail('The OtherUser user not found. Did you load the fixtures?');
        }

        self::$otherUser = $otherUser->getId() ?? 0;
    }

    public function setUp(): void
    {
        self::setToken('user');
        parent::setUp();
    }

    public function testCreateUser(): void
    {
        $response = $this->client->request('POST', '/api/users', ['json' => [
            'email' => 'foo-test-ok@example.org',
            'plainPassword' => 'foo-test1',
            'pseudonym' => 'Foo Test1',
        ]]);

        self::assertResponseStatusCodeSame(403);
        self::assertJsonContains([
            '@context' => '/api/contexts/Error',
            '@type' => 'hydra:Error',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'Access Denied.',
        ]);
    }

    public function testDeleteUser(): void
    {
        $this->client->request('DELETE', '/api/users/'.self::$otherUser);
        self::assertHydraAccessDenied();
    }

    public function testGet(): void
    {
        $response = $this->client->request('GET', '/api/users');
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
        $response = $this->client->request('GET', '/api/users');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
        ]);

        self::assertHydraCollectionOnlyContainsKeysInMember(['@id', '@type', 'pseudonym', 'admin'], $response);
    }

    public function testPatchUser(): void
    {
        self::setModePatch();
        $this->client->request('PATCH', '/api/users/'.self::$otherUser, ['json' => [
            'email' => 'bar-test-ok@example.org',
            'plainPassword' => 'bar-test1',
            'pseudonym' => 'bar Test1',
        ]]);
        self::unsetModePatch();
        self::assertHydraAccessDenied();
    }

    public function testPutUser(): void
    {
        $this->client->request('PUT', '/api/users/'.self::$otherUser, ['json' => [
            'email' => 'BAR-test-ok@example.org',
            'plainPassword' => 'BAR-test1',
            'pseudonym' => 'BAR Test1',
        ]]);
        self::assertHydraAccessDenied();
    }
}
