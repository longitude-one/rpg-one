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

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testConstruct(): void
    {
        $user = new User();
        self::assertSame(['ROLE_USER'], $user->getRoles());
        self::assertNotNull($user->getUserIdentifier());
        self::assertEmpty($user->getUserIdentifier());
        self::assertNull($user->getPseudonym());
        self::assertNull($user->getEmail());
        self::assertNull($user->getId());
    }

    public function testUserIdentifier(): void
    {
        $user = new User();
        self::assertEmpty($user->getUserIdentifier());

        $actual = $expected = 'foo';
        $user->setEmail($actual);
        self::assertSame($expected, $user->getEmail());
        self::assertNotEmpty($user->getUserIdentifier());
        self::assertSame($expected, $user->getUserIdentifier());
    }
}
