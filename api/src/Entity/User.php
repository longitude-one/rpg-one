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
use App\Repository\UserRepository;
use App\State\UserPasswordHasher;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['pseudonym'], message: 'There is already an account with this username')]
#[ApiResource(
    shortName: 'User',
    description: 'User entity',
    types: 'https://schema.org/Person',
    normalizationContext: ['groups' => ['anonymous:read']],
    denormalizationContext: ['groups' => ['anonymous:write']],
    mercure: true
)]
#[Delete(security: 'is_granted("ROLE_ADMIN")')]
#[Get]
#[GetCollection]
#[Post(security: 'is_granted("ROLE_ADMIN") or !is_granted("ROLE_USER")', processor: UserPasswordHasher::class)]
#[Patch(security: 'is_granted("ROLE_ADMIN") or object == user', processor: UserPasswordHasher::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface // , JwtUserInterface
{
    #[ORM\Column(length: 180, unique: true)]
    #[ApiProperty(types: ['https://schema.org/email'])]
    #[Groups(['anonymous:write', 'admin:read', 'owner:read', 'owner:write'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ApiProperty(types: ['https://schema.org/accessCode'])]
    #[Groups(['anonymous:write', 'admin:write', 'owner:write'])]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255, unique: true)]
    #[ApiProperty(types: ['https://schema.org/name'])]
    #[Groups(['anonymous:read', 'anonymous:write', 'owner:read', 'owner:write', 'admin:read', 'admin:write'])]
    #[Assert\NotBlank]
    private ?string $pseudonym = null;

    /**
     * @var string[]
     */
    #[ORM\Column]
    #[ApiProperty(jsonSchemaContext: [
        'type' => 'array',
        'items' => ['type' => 'string'],
    ])]
    private array $roles = [];

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

//
//    public static function createFromPayload($username, array $payload): self
//    {
//        $user = new User();
//        $user
//            ->setId($payload['id']??0)
//            ->setEmail($payload['username']??'')
//            ->setUsername($payload['username']??'')
//            ->setRoles($payload['roles']??[])
//            ->setPassword($payload['password']??'');
//
//        return $user;
//    }
//
//    private function setId(int $id): self
//    {
//        $this->id = $id;
//
//        return $this;
//    }
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getPseudonym(): ?string
    {
        return $this->pseudonym;
    }

    /**
     * @return string[]
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    #[Groups(['anonymous:read', 'user:read'])]
    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function setPseudonym(string $pseudonym): self
    {
        $this->pseudonym = $pseudonym;

        return $this;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
