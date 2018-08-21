<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180819230818 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE downloadable_file ADD group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE downloadable_file ADD CONSTRAINT FK_F90A22BFFE54D947 FOREIGN KEY (group_id) REFERENCES file_group (id)');
        $this->addSql('CREATE INDEX IDX_F90A22BFFE54D947 ON downloadable_file (group_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE downloadable_file DROP FOREIGN KEY FK_F90A22BFFE54D947');
        $this->addSql('DROP INDEX IDX_F90A22BFFE54D947 ON downloadable_file');
        $this->addSql('ALTER TABLE downloadable_file DROP group_id');
    }
}
