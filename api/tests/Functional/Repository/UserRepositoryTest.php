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

namespace App\Tests\Functional\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private readonly UserRepository $userRepository;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $registry = $kernel->getContainer()->get('doctrine');
        if (!$registry instanceof Registry) {
            self::fail('Doctrine registry not loaded');
        }
        $entityManager = $registry->getManager();
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function testFindOneByEmail(): void
    {
        $user = $this->userRepository->findOneByEmail('Non-Existent-Email@foo.example.org');
        self::assertNull($user);

        $actual = $expected = 'admin@example.org';
        $user = $this->userRepository->findOneByEmail($actual);

        self::assertNotNull($user);
        self::assertInstanceOf(User::class, $user);
        self::assertSame($expected, $user->getEmail());
    }

    public function testFindOneByPseudonym(): void
    {
        $user = $this->userRepository->findOneByPseudonym('Non-Existent-User-Foo-Bar');
        self::assertNull($user);

        $actual = $expected = 'DefaultAdmin';
        $user = $this->userRepository->findOneByPseudonym($actual);

        self::assertNotNull($user);
        self::assertInstanceOf(User::class, $user);
        self::assertSame($expected, $user->getPseudonym());
    }
}
