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
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;

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

    private static function getEntityManager(): EntityManagerInterface
    {
        $registry = self::getContainer()->get('doctrine');

        if ($registry instanceof Registry) {
            return $registry->getManager();
        }

        self::fail('Doctrine registry not loaded');
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
