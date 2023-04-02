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

use App\Entity\Conversation;
use App\Repository\ConversationRepository;
use JetBrains\PhpStorm\ArrayShape;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Conversation>
 *
 * @method        Conversation|Proxy<Conversation>       create(array|callable $attributes = [])
 * @method static Conversation|Proxy<Conversation>       createOne(array $attributes = [])
 * @method static Conversation|Proxy<Conversation>       find(object|array|mixed $criteria)
 * @method static Conversation|Proxy<Conversation>       findOrCreate(array $attributes)
 * @method static Conversation|Proxy<Conversation>       first(string $sortedField = 'id')
 * @method static Conversation|Proxy<Conversation>       last(string $sortedField = 'id')
 * @method static Conversation|Proxy<Conversation>       random(array $attributes = [])
 * @method static Conversation|Proxy<Conversation>       randomOrCreate(array $attributes = [])
 * @method static ConversationRepository|RepositoryProxy repository()
 * @method static Conversation[]|Proxy<Conversation>[]   all()
 * @method static Conversation[]|Proxy<Conversation>[]   createMany(int $number, array|callable $attributes = [])
 * @method static Conversation[]|Proxy<Conversation>[]   createSequence(array|callable $sequence)
 * @method static Conversation[]|Proxy<Conversation>[]   findBy(array $attributes)
 * @method static Conversation[]|Proxy<Conversation>[]   randomRange(int $min, int $max, array $attributes = [])
 * @method static Conversation[]|Proxy<Conversation>[]   randomSet(int $number, array $attributes = [])
 */
final class ConversationFactory extends ModelFactory
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
     * @return Conversation[]|Proxy<Conversation>[]
     */
    public static function createTestConversations(): array
    {
        $conversations[] = self::createOne([
            'abstract' => null,
            'headline' => null,
            'title' => 'First conversation',
        ]);

        // Other conversation
        $conversations[] = self::createOne([
            'abstract' => null,
            'headline' => null,
            'title' => 'Second conversation',
        ]);

        // Owner : This conversation is used to test the owner feature
        $conversations[] = self::createOne([
            'abstract' => null,
            'headline' => null,
            'title' => 'Third conversation',
        ]);

        // Admin conversation
        $conversations[] = self::createOne([
            'abstract' => null,
            'headline' => null,
            'title' => 'Fourth conversation',
        ]);

        return $conversations;
    }

    protected static function getClass(): string
    {
        return Conversation::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    #[ArrayShape(['email' => 'string', 'password' => 'string', 'pseudonym' => 'string'])]
    protected function getDefaults(): array
    {
        return [
            'abstract' => self::faker()->paragraph(3),
            'headline' => self::faker()->paragraph(4),
            'title' => self::faker()->sentence(),
        ];
    }
}
