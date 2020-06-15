<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200613135301 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE adverts CHANGE city_id city_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70C0C6E6E7927C74 ON agency (email)');
        $this->addSql('ALTER TABLE agency RENAME INDEX fk_70c0c6e68bac62af TO IDX_70C0C6E68BAC62AF');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE city RENAME INDEX province_id TO IDX_2D5B0234E946114A');
        $this->addSql('ALTER TABLE members RENAME INDEX email TO UNIQ_45A0D2FFE7927C74');
        $this->addSql('ALTER TABLE province DROP FOREIGN KEY FK_4ADAD40B8BAC62AF');
        $this->addSql('ALTER TABLE province ADD CONSTRAINT FK_4ADAD40B98260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE quartier DROP FOREIGN KEY FK_FEE8962DE946114A');
        $this->addSql('ALTER TABLE quartier ADD CONSTRAINT FK_FEE8962D8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE quartier RENAME INDEX city_id TO IDX_FEE8962D8BAC62AF');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE adverts CHANGE city_id city_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_70C0C6E6E7927C74 ON agency');
        $this->addSql('ALTER TABLE agency RENAME INDEX idx_70c0c6e68bac62af TO FK_70C0C6E68BAC62AF');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234E946114A');
        $this->addSql('ALTER TABLE city RENAME INDEX idx_2d5b0234e946114a TO province_id');
        $this->addSql('ALTER TABLE members RENAME INDEX uniq_45a0d2ffe7927c74 TO email');
        $this->addSql('ALTER TABLE province DROP FOREIGN KEY FK_4ADAD40B98260155');
        $this->addSql('ALTER TABLE province ADD CONSTRAINT FK_4ADAD40B8BAC62AF FOREIGN KEY (region_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE quartier DROP FOREIGN KEY FK_FEE8962D8BAC62AF');
        $this->addSql('ALTER TABLE quartier ADD CONSTRAINT FK_FEE8962DE946114A FOREIGN KEY (city_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE quartier RENAME INDEX idx_fee8962d8bac62af TO city_id');
    }
}
