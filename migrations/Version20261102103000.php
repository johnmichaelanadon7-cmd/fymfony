<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add optional product image filenames for uploaded catalog photos.
 */
final class Version20261102103000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add the optional image filename column to products.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product ADD image_filename VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product DROP image_filename');
    }
}
