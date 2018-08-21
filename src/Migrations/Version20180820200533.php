<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180820200533 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE downloadable_file (id INT AUTO_INCREMENT NOT NULL, group_id INT NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, upload_time DATETIME NOT NULL, INDEX IDX_F90A22BFFE54D947 (group_id), UNIQUE INDEX unique_file_in_group (name, group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE downloadable_file ADD CONSTRAINT FK_F90A22BFFE54D947 FOREIGN KEY (group_id) REFERENCES file_group (id)');
        $this->addSql('DROP TABLE ecommerce_products');
        $this->addSql('ALTER TABLE download ADD CONSTRAINT FK_781A827093CB796C FOREIGN KEY (file_id) REFERENCES downloadable_file (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C743F4F2989D9B62 ON file_group (slug)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE download DROP FOREIGN KEY FK_781A827093CB796C');
        $this->addSql('CREATE TABLE ecommerce_products (id INT AUTO_INCREMENT NOT NULL, group_id INT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, path VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, upload_time DATETIME NOT NULL, UNIQUE INDEX unique_file_in_group (name, group_id), INDEX IDX_28CF0AEFFE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ecommerce_products ADD CONSTRAINT FK_28CF0AEFFE54D947 FOREIGN KEY (group_id) REFERENCES file_group (id)');
        $this->addSql('DROP TABLE downloadable_file');
        $this->addSql('DROP INDEX UNIQ_C743F4F2989D9B62 ON file_group');
    }
}
