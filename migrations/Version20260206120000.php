<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260206120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add color column to event table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE event ADD color VARCHAR(10) NOT NULL DEFAULT 'rouge'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event DROP color');
    }
}
