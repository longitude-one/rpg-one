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

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ConversationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
#[ORM\Table(name: '`conversation`')]
#[ApiResource(
    shortName: 'Conversation',
    description: 'One or more messages between Characters on a particular topic. Individual messages can be linked to the conversation with isPartOf or hasPart properties.',
    types: 'https://schema.org/Conversation',
    // TODO Normalization and denormalization should be dynamic and should depend on the application settings
    normalizationContext: ['groups' => ['anonymous:read']],
    denormalizationContext: ['groups' => ['anonymous:write']],
    mercure: true
)]
// FIXME Setup security with owner
#[Delete(security: 'is_granted("ROLE_ADMIN")')]
#[Get]
#[GetCollection]
#[Post(security: 'is_granted("ROLE_ADMIN")')]
#[Patch(security: 'is_granted("ROLE_ADMIN")')]
class Conversation // TODO Add the CommentableInterface
{
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[ApiProperty(description: 'A short description that summarizes the Conversation.', types: 'https://schema.org/abstract')]
    #[Groups(['anonymous:read', 'player:read', 'owner:read', 'owner:write', 'admin:read', 'admin:write'])]
    private ?string $abstract = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[ApiProperty(description: 'Headline of the conversation', types: 'https://schema.org/headline')]
    #[Groups(['anonymous:read', 'player:read', 'owner:read', 'owner:write', 'admin:read', 'admin:write'])]
    private ?string $headline = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[ApiProperty(description: 'The title of the conversation', types: 'https://schema.org/name')]
    #[Groups(['anonymous:read', 'player:read', 'owner:read', 'owner:write', 'admin:read', 'admin:write'])]
    private ?string $title = null;

    public function getAbstract(): ?string
    {
        return $this->abstract;
    }

    public function getHeadline(): ?string
    {
        return $this->headline;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setAbstract(?string $abstract): self
    {
        $this->abstract = $abstract;

        return $this;
    }

    public function setHeadline(?string $headline): Conversation
    {
        $this->headline = $headline;

        return $this;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
