<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180827031254 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE downloadable_file DROP FOREIGN KEY FK_F90A22BFFE54D947');
        $this->addSql('CREATE TABLE folder (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_ECA209CD989D9B62 (slug), INDEX IDX_ECA209CD727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE folder ADD CONSTRAINT FK_ECA209CD727ACA70 FOREIGN KEY (parent_id) REFERENCES folder (id)');
        $this->addSql('DROP TABLE file_group');
        $this->addSql('DROP INDEX IDX_F90A22BFFE54D947 ON downloadable_file');
        $this->addSql('DROP INDEX unique_file_in_group ON downloadable_file');
        $this->addSql('ALTER TABLE downloadable_file CHANGE group_id folder_id INT NOT NULL');
        $this->addSql('ALTER TABLE downloadable_file ADD CONSTRAINT FK_F90A22BF162CB942 FOREIGN KEY (folder_id) REFERENCES folder (id)');
        $this->addSql('CREATE INDEX IDX_F90A22BF162CB942 ON downloadable_file (folder_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_file_in_group ON downloadable_file (name, folder_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE downloadable_file DROP FOREIGN KEY FK_F90A22BF162CB942');
        $this->addSql('ALTER TABLE folder DROP FOREIGN KEY FK_ECA209CD727ACA70');
        $this->addSql('CREATE TABLE file_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, slug VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, UNIQUE INDEX UNIQ_C743F4F2989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE folder');
        $this->addSql('DROP INDEX IDX_F90A22BF162CB942 ON downloadable_file');
        $this->addSql('DROP INDEX unique_file_in_group ON downloadable_file');
        $this->addSql('ALTER TABLE downloadable_file CHANGE folder_id group_id INT NOT NULL');
        $this->addSql('ALTER TABLE downloadable_file ADD CONSTRAINT FK_F90A22BFFE54D947 FOREIGN KEY (group_id) REFERENCES file_group (id)');
        $this->addSql('CREATE INDEX IDX_F90A22BFFE54D947 ON downloadable_file (group_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_file_in_group ON downloadable_file (name, group_id)');
    }
}
