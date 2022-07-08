<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220705131122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_user_client');
        $this->addSql('ALTER TABLE user DROP token');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_user_client (user_id INT NOT NULL, user_client_id INT NOT NULL, INDEX IDX_B0DD6FDA76ED395 (user_id), INDEX IDX_B0DD6FD190BE4C5 (user_client_id), PRIMARY KEY(user_id, user_client_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_user_client ADD CONSTRAINT FK_B0DD6FD190BE4C5 FOREIGN KEY (user_client_id) REFERENCES user_client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user_client ADD CONSTRAINT FK_B0DD6FDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD token VARCHAR(255) DEFAULT NULL');
    }
}
