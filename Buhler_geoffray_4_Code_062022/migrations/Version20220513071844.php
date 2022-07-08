<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220513071844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_client (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(30) NOT NULL, lastname VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_client_user (user_client_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F514DCBE190BE4C5 (user_client_id), INDEX IDX_F514DCBEA76ED395 (user_id), PRIMARY KEY(user_client_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_client_user ADD CONSTRAINT FK_F514DCBE190BE4C5 FOREIGN KEY (user_client_id) REFERENCES user_client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_client_user ADD CONSTRAINT FK_F514DCBEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_client_user DROP FOREIGN KEY FK_F514DCBE190BE4C5');
        $this->addSql('DROP TABLE user_client');
        $this->addSql('DROP TABLE user_client_user');
    }
}
