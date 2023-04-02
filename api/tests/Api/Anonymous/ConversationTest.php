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

use App\Entity\Conversation;
use App\Tests\Api\MyApiTest;

class ConversationTest extends MyApiTest
{
    private static int $firstConversation;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $conversation = self::getEntityManager()->getRepository(Conversation::class)->findOneByTitle('First conversation');
        if (null === $conversation) {
            self::fail('The First Conversation was not found. Did you load the fixtures?');
        }

        self::$firstConversation = $conversation->getId() ?? 0;
    }

    public function testCreateConversation(): void
    {
        static::createClient()->request('POST', '/api/conversations');
        self::assertJwtTokenNotFound();
    }

    public function testDelete(): void
    {
        static::createClient()->request('DELETE', '/api/conversations/'.self::$firstConversation);
        self::assertJwtTokenNotFound();
    }

    public function testGet(): void
    {
        $response = static::createClient()->request('GET', '/api/conversations');
        $url = $response->toArray()['hydra:member'][0]['@id'];

        $response = static::createClient()->request('GET', $url);
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/api/contexts/Conversation',
            '@id' => $url,
            '@type' => 'https://schema.org/Conversation',
        ]);

        self::assertOnlyContainsKeys(['@context', '@id', '@type', 'abstract', 'headline', 'title'], $response);
    }

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/conversations');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/api/contexts/Conversation',
            '@id' => '/api/conversations',
            '@type' => 'hydra:Collection',
        ]);

        self::assertHydraCollectionOnlyContainsKeysInMember(['@id', '@type', 'abstract', 'headline', 'title'], $response);
    }

    public function testPatch(): void
    {
        static::createClient()->request('PATCH', '/api/conversations/'.self::$firstConversation, ['headers' => [
            'Content-type' => 'application/merge-patch+json',
        ]]);
        self::assertJwtTokenNotFound();
    }

    public function testPut(): void
    {
        static::createClient()->request('PUT', '/api/conversations/'.self::$firstConversation, ['json' => [
            'abstract' => 'Foo bar',
            'headline' => 'Bar',
            'title' => 'Foo',
        ]]);
        self::assertMethodNotAllowed();
    }
}
