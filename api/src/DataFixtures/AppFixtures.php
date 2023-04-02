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

namespace App\DataFixtures;

use App\DataFixtures\Factory\ConversationFactory;
use App\DataFixtures\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // TODO Load only on dev and test environment
        UserFactory::createMany(10);
        UserFactory::createTestUsers();
        ConversationFactory::createMany(20);
        ConversationFactory::createTestConversations();
    }
}
