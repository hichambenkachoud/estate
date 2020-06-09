<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200531163036 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agency ADD city_id INT NOT NULL');
        $this->addSql('ALTER TABLE agency ADD CONSTRAINT FK_70C0C6E68BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_70C0C6E68BAC62AF ON agency (city_id)');
        $this->addSql('ALTER TABLE members RENAME INDEX email TO UNIQ_45A0D2FFE7927C74');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agency DROP FOREIGN KEY FK_70C0C6E68BAC62AF');
        $this->addSql('DROP INDEX IDX_70C0C6E68BAC62AF ON agency');
        $this->addSql('ALTER TABLE agency DROP city_id');
        $this->addSql('ALTER TABLE members RENAME INDEX uniq_45a0d2ffe7927c74 TO email');
    }
}
