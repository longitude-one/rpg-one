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

namespace App\DataFixtures\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<User>
 *
 * @method        User|Proxy<User>               create(array|callable $attributes = [])
 * @method static User|Proxy<User>               createOne(array $attributes = [])
 * @method static User|Proxy<User>               find(object|array|mixed $criteria)
 * @method static User|Proxy<User>               findOrCreate(array $attributes)
 * @method static User|Proxy<User>               first(string $sortedField = 'id')
 * @method static User|Proxy<User>               last(string $sortedField = 'id')
 * @method static User|Proxy<User>               random(array $attributes = [])
 * @method static User|Proxy<User>               randomOrCreate(array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method static User[]|Proxy<User>[]           all()
 * @method static User[]|Proxy<User>[]           createMany(int $number, array|callable $attributes = [])
 * @method static User[]|Proxy<User>[]           createSequence(array|callable $sequence)
 * @method static User[]|Proxy<User>[]           findBy(array $attributes)
 * @method static User[]|Proxy<User>[]           randomRange(int $min, int $max, array $attributes = [])
 * @method static User[]|Proxy<User>[]           randomSet(int $number, array $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    public const PSEUDONYMS = [
        'FlamingInferno',
        'ScaleSorcerer',
        'TheDragonWithBadBreath',
        'BurnedOut',
        'ForgotMyOwnName',
        'ClumsyClaws',
        'HoarderOfUselessTrinkets',
    ];

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    /**
     * @return User[]|Proxy<User>[]
     */
    public static function createTestUsers(): array
    {
        // Default user
        $users[] = self::createOne([
            'email' => 'user@example.org',
            'password' => 'password',
            'pseudonym' => 'DefaultUser',
        ]);

        // Owner : This user is used to test the owner feature
        $users[] = self::createOne([
            'email' => 'owner@example.org',
            'password' => 'password',
            'pseudonym' => 'The Owner',
        ]);

        // Admin user
        $users[] = self::createOne([
            'email' => 'admin@example.org',
            'password' => 'password',
            'pseudonym' => 'DefaultAdmin',
            'roles' => ['ROLE_ADMIN'],
        ]);

        return $users;
    }

    protected static function getClass(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    #[ArrayShape(['email' => 'string', 'password' => 'string', 'pseudonym' => 'string'])]
    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'password' => 'password',
            'pseudonym' => self::faker()->randomElement(self::PSEUDONYMS).self::faker()->randomNumber(3),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function (User $user): void {
                $user->setPassword($this->passwordHasher->hashPassword(
                    $user,
                    $user->getPassword() ?? 'password')
                );
            })
        ;
    }
}
