<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180620195449 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_product DROP FOREIGN KEY FK_8B471AA712469DE2');
        $this->addSql('DROP INDEX IDX_8B471AA712469DE2 ON user_product');
        $this->addSql('ALTER TABLE user_product DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user_product DROP category_id');
        $this->addSql('ALTER TABLE user_product ADD PRIMARY KEY (user_id, product_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_product DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user_product ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_product ADD CONSTRAINT FK_8B471AA712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_8B471AA712469DE2 ON user_product (category_id)');
        $this->addSql('ALTER TABLE user_product ADD PRIMARY KEY (user_id, product_id, category_id)');
    }
}
