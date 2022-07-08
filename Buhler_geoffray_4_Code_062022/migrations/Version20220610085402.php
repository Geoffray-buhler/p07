<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220610085402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_client_user');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27190BE4C5');
        $this->addSql('DROP INDEX IDX_29A5EC27190BE4C5 ON produit');
        $this->addSql('ALTER TABLE produit DROP user_client_id');
        $this->addSql('ALTER TABLE user_client ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_client ADD CONSTRAINT FK_A2161F68A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_A2161F68A76ED395 ON user_client (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_client_user (user_client_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F514DCBE190BE4C5 (user_client_id), INDEX IDX_F514DCBEA76ED395 (user_id), PRIMARY KEY(user_client_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_client_user ADD CONSTRAINT FK_F514DCBE190BE4C5 FOREIGN KEY (user_client_id) REFERENCES user_client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_client_user ADD CONSTRAINT FK_F514DCBEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit ADD user_client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27190BE4C5 FOREIGN KEY (user_client_id) REFERENCES user_client (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27190BE4C5 ON produit (user_client_id)');
        $this->addSql('ALTER TABLE user_client DROP FOREIGN KEY FK_A2161F68A76ED395');
        $this->addSql('DROP INDEX IDX_A2161F68A76ED395 ON user_client');
        $this->addSql('ALTER TABLE user_client DROP user_id');
    }
}
