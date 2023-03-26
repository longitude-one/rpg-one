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

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class MyApiTest extends ApiTestCase
{
    /**
     * @param string[] $keys
     */
    protected static function assertContainsKeys(array $keys, ResponseInterface $response, bool $strict = false): void
    {
        self::assertJson($response->getContent());
        $content = $response->toArray();
        self::assertIsArray($content);

        foreach ($keys as $key) {
            self::assertArrayHasKey($key, $content, 'The key '.$key.' is missing in the response. It only contains '.implode(', ', array_keys($content)));
        }

        if ($strict) {
            self::assertCount(
                count($keys),
                $content,
                'The response contains more keys than expected. It contains '.implode(', ', array_keys($content))
            );
        }
    }

    protected static function assertHydraCollection(): void
    {
        self::assertJsonContains(['@type' => 'hydra:Collection']);
    }

    /**
     * @param string[] $keys
     */
    protected static function assertHydraCollectionContainsKeysInMember(array $keys, ResponseInterface $response, bool $strict = false): void
    {
        self::assertJson($response->getContent());
        self::assertHydraCollection();

        $hydraMembers = $response->toArray()['hydra:member'];
        $firstMember = array_shift($hydraMembers);
        if (null === $firstMember) {
            self::fail('The collection is empty! Did you forget to load initial data fixtures? Try to launch `php bin/console doctrine:fixtures:load --env=test`');
        }

        foreach ($keys as $key) {
            self::assertArrayHasKey($key, $firstMember, 'The key '.$key.' is missing in the collection. It only contains '.implode(', ', array_keys($firstMember)));
        }

        self::assertCount(
            count($keys),
            $firstMember,
            'The collection contains more keys than expected. It contains '.implode(', ', array_keys($firstMember))
        );
    }

    /**
     * @param string[] $keys
     */
    protected static function assertHydraCollectionNotContainsKeysInMember(array $keys, ResponseInterface $response): void
    {
        self::assertJson($response->getContent());
        self::assertHydraCollection();

        $hydraMembers = $response->toArray()['hydra:member'];
        self::assertNotEmpty($hydraMembers, 'The collection is empty! I cannot check if it contains keys');

        $firstMember = array_shift($hydraMembers);
        foreach ($keys as $key) {
            self::assertArrayNotHasKey($key, $firstMember);
        }
    }

    /**
     * @param string[] $keys
     */
    protected static function assertHydraCollectionOnlyContainsKeysInMember(array $keys, ResponseInterface $response): void
    {
        self::assertHydraCollectionContainsKeysInMember($keys, $response, true);
    }

    /**
     * @param string[] $keys
     */
    protected static function assertOnlyContainsKeys(array $keys, ResponseInterface $response): void
    {
        self::assertContainsKeys($keys, $response, true);
    }
}
