<?php

declare(strict_types=1);

/**
 * This file is part of the RPG-One Project
 *
 * PHP 8.2 | Symfony 6.2+
 *
 * Copyright LongitudeOne - Alexandre Tranchant
 * Copyright 2023 - 2023
 *
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230402143505 extends AbstractMigration
{
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "conversation_id_seq" CASCADE');
        $this->addSql('DROP TABLE "conversation"');
        $this->addSql('ALTER INDEX uniq_8d93d6493654b190 RENAME TO uniq_8d93d649f85e0677');
    }

    public function getDescription(): string
    {
        return 'Add/drop conversation table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "conversation_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "conversation" (id INT NOT NULL, abstract TEXT DEFAULT NULL, headline TEXT DEFAULT NULL, title TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER INDEX uniq_8d93d649f85e0677 RENAME TO UNIQ_8D93D6493654B190');
    }
}
