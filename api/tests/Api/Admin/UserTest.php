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

namespace App\Tests\Api\Admin;

use App\Tests\Api\AuthenticationTest;

class UserTest extends AuthenticationTest
{
    public function setUp(): void
    {
        self::setToken('admin');
        parent::setUp();
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

        self::assertHydraCollectionOnlyContainsKeysInMember(['@id', '@type', 'pseudonym', 'email', 'admin'], $response);
    }

    public function testManageUser(): void
    {
        $this->client->request('POST', '/api/users', ['json' => [
            'email' => 'foo-test-will-failed',
            'plainPassword' => 'foo-test1',
            'pseudonym' => 'Foo Test1',
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
        ]);

        $response = $this->client->request('POST', '/api/users', ['json' => [
            'email' => 'foo-test-ok@example.org',
            'plainPassword' => 'foo-test1',
            'pseudonym' => 'Foo Test1',
        ]]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'https://schema.org/Person',
        ]);
        self::assertOnlyContainsKeys(['@context', '@id', '@type', 'pseudonym', 'email', 'admin'], $response);
        $userToManipulate = $userToDelete = $response->toArray()['@id'];

        $this->client->request('PUT', $userToManipulate, ['json' => [
            'email' => 'BAR-test-ok@example.org',
            'plainPassword' => 'BAR-test1',
            'pseudonym' => 'BAR Test1',
        ]]);
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'https://schema.org/Person',
        ]);
        self::assertOnlyContainsKeys(['@context', '@id', '@type', 'pseudonym', 'email', 'admin'], $response);

        self::setModePatch();
        $this->client->request('PATCH', $userToManipulate, ['json' => [
            'email' => 'bar-test-ok@example.org',
            'plainPassword' => 'bar-test1',
            'pseudonym' => 'bar Test1',
        ]]);
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'https://schema.org/Person',
        ]);
        self::assertOnlyContainsKeys(['@context', '@id', '@type', 'pseudonym', 'email', 'admin'], $response);

        self::unsetModePatch();
        $response = $this->client->request('DELETE', $userToDelete);
        self::assertResponseIsSuccessful();
        self::assertEmpty($response->getContent());
    }
}
