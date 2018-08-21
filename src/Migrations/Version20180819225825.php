<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180819225825 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE download ADD file_id INT DEFAULT NULL, DROP file_name');
        $this->addSql('ALTER TABLE download ADD CONSTRAINT FK_781A827093CB796C FOREIGN KEY (file_id) REFERENCES downloadable_file (id)');
        $this->addSql('CREATE INDEX IDX_781A827093CB796C ON download (file_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE download DROP FOREIGN KEY FK_781A827093CB796C');
        $this->addSql('DROP INDEX IDX_781A827093CB796C ON download');
        $this->addSql('ALTER TABLE download ADD file_name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP file_id');
    }
}
