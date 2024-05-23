<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522152844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, phome_team_id INT NOT NULL, guest_team_id INT NOT NULL, week_id INT NOT NULL, home_score INT NOT NULL, guest_score INT NOT NULL, finished TINYINT(1) NOT NULL, INDEX IDX_232B318C5219EF9F (phome_team_id), INDEX IDX_232B318C69A91CE2 (guest_team_id), INDEX IDX_232B318CC86F3B2F (week_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(16) NOT NULL, datetime DATETIME NOT NULL, finished_games INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stat (id INT AUTO_INCREMENT NOT NULL, team_id INT NOT NULL, goal INT NOT NULL, goalc INT NOT NULL, games_quantity INT NOT NULL, UNIQUE INDEX UNIQ_20B8FF21296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE week (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, name VARCHAR(16) NOT NULL, finished TINYINT(1) NOT NULL, INDEX IDX_5B5A69C04EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C5219EF9F FOREIGN KEY (phome_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C69A91CE2 FOREIGN KEY (guest_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CC86F3B2F FOREIGN KEY (week_id) REFERENCES week (id)');
        $this->addSql('ALTER TABLE stat ADD CONSTRAINT FK_20B8FF21296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE week ADD CONSTRAINT FK_5B5A69C04EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C5219EF9F');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C69A91CE2');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CC86F3B2F');
        $this->addSql('ALTER TABLE stat DROP FOREIGN KEY FK_20B8FF21296CD8AE');
        $this->addSql('ALTER TABLE week DROP FOREIGN KEY FK_5B5A69C04EC001D1');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE stat');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE week');
    }
}
