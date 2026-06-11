<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260611065648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD self_exclusion_end DATETIME DEFAULT NULL, ADD daily_deposit_limit DOUBLE PRECISION DEFAULT NULL, ADD weekly_deposit_limit DOUBLE PRECISION DEFAULT NULL, ADD daily_bet_limit DOUBLE PRECISION DEFAULT NULL, ADD weekly_bet_limit DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP self_exclusion_end, DROP daily_deposit_limit, DROP weekly_deposit_limit, DROP daily_bet_limit, DROP weekly_bet_limit');
    }
}
