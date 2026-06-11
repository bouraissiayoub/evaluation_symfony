<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260611073152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE issue (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, current_odds DOUBLE PRECISION NOT NULL, total_amount_bet DOUBLE PRECISION NOT NULL, sport_event_id INT NOT NULL, INDEX IDX_12AD233E47551731 (sport_event_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sport_event (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, sport VARCHAR(255) NOT NULL, participants VARCHAR(255) NOT NULL, event_date DATETIME NOT NULL, status VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E47551731 FOREIGN KEY (sport_event_id) REFERENCES sport_event (id)');
        $this->addSql('ALTER TABLE user CHANGE self_exclusion_end self_exclusion_end VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E47551731');
        $this->addSql('DROP TABLE issue');
        $this->addSql('DROP TABLE sport_event');
        $this->addSql('ALTER TABLE user CHANGE self_exclusion_end self_exclusion_end DATETIME DEFAULT NULL');
    }
}
