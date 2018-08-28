<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180828034324 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE downloadable_file DROP FOREIGN KEY FK_F90A22BF162CB942');
        $this->addSql('ALTER TABLE folder DROP FOREIGN KEY FK_ECA209CD727ACA70');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE folder');
        $this->addSql('DROP INDEX unique_file_in_folder ON downloadable_file');
        $this->addSql('DROP INDEX IDX_F90A22BF162CB942 ON downloadable_file');
        $this->addSql('ALTER TABLE downloadable_file ADD project_id INT NOT NULL, DROP folder_id');
        $this->addSql('ALTER TABLE downloadable_file ADD CONSTRAINT FK_F90A22BF166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_F90A22BF166D1F9C ON downloadable_file (project_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_file_in_project ON downloadable_file (name, project_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE downloadable_file DROP FOREIGN KEY FK_F90A22BF166D1F9C');
        $this->addSql('CREATE TABLE folder (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_ECA209CD727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE folder ADD CONSTRAINT FK_ECA209CD727ACA70 FOREIGN KEY (parent_id) REFERENCES folder (id)');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP INDEX IDX_F90A22BF166D1F9C ON downloadable_file');
        $this->addSql('DROP INDEX unique_file_in_project ON downloadable_file');
        $this->addSql('ALTER TABLE downloadable_file ADD folder_id INT DEFAULT NULL, DROP project_id');
        $this->addSql('ALTER TABLE downloadable_file ADD CONSTRAINT FK_F90A22BF162CB942 FOREIGN KEY (folder_id) REFERENCES folder (id)');
        $this->addSql('CREATE UNIQUE INDEX unique_file_in_folder ON downloadable_file (name, folder_id)');
        $this->addSql('CREATE INDEX IDX_F90A22BF162CB942 ON downloadable_file (folder_id)');
    }
}
