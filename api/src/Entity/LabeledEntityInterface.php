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

namespace App\Entity;

interface LabeledEntityInterface
{
    public function getPitch(): ?string;

    public function getText(): ?string;

    public function getTitle(): ?string;
}
