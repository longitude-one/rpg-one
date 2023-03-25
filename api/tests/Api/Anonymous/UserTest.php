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

use App\Tests\Api\MyApiTest;

class UserTest extends MyApiTest
{
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

    public function testGetCollection(): void
    {
        static::createClient()->request('GET', '/api/users');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 12,
        ]);
    }
}
