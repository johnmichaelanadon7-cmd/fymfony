<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add the initial bakery categories used by the Harvest Bread Co. catalog.
 */
final class Version20261102094500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed the default bakery product categories.';
    }

    public function up(Schema $schema): void
    {
        $categories = [
            ['Pandesal', 'Classic and flavored Filipino bread rolls.'],
            ['Loaf Bread', 'Sliced breads for sandwiches, toast, and everyday meals.'],
            ['Sweet Bread', 'Soft and sweet favorites such as Spanish bread and ensaymada.'],
            ['Specialty Bread', 'Savory and signature breads made with special ingredients.'],
            ['Pastries', 'Croissants, cinnamon rolls, and other baked pastries.'],
            ['Cakes', 'Whole cakes and individual cake slices.'],
            ['Cookies', 'Freshly baked cookies in different flavors.'],
            ['Donuts', 'Glazed, filled, and topped donuts.'],
        ];

        foreach ($categories as [$name, $description]) {
            $this->addSql(
                'INSERT INTO category (name, description) '
                .'SELECT :name, :description WHERE NOT EXISTS '
                .'(SELECT 1 FROM category WHERE name = :existingName)',
                ['name' => $name, 'description' => $description, 'existingName' => $name],
            );
        }
    }

    public function down(Schema $schema): void
    {
        // Keep any seeded category that has products assigned to it.
        $this->addSql(<<<'SQL'
            DELETE FROM category
            WHERE name IN (
                'Pandesal', 'Loaf Bread', 'Sweet Bread', 'Specialty Bread',
                'Pastries', 'Cakes', 'Cookies', 'Donuts'
            )
            AND NOT EXISTS (
                SELECT 1 FROM product WHERE product.category_id = category.id
            )
            SQL);
    }
}
