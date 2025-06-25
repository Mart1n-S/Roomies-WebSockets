<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250522135805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE friendship (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', applicant_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', recipient_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_7234A45F97139001 (applicant_id), INDEX IDX_7234A45FE92F8F78 (recipient_id), INDEX idx_recipient_status (recipient_id, status), UNIQUE INDEX unique_friendship (applicant_id, recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE message (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', sender_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', room_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', content LONGTEXT DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307F54177093 (room_id), INDEX idx_message_room_created (room_id, created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE room (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', name VARCHAR(30) DEFAULT NULL, is_group TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE room_user (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', user_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', room_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', role VARCHAR(255) NOT NULL, joined_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_EE973C2DA76ED395 (user_id), INDEX IDX_EE973C2D54177093 (room_id), INDEX idx_room_user (room_id, role), UNIQUE INDEX unique_user_room (user_id, room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship ADD CONSTRAINT FK_7234A45F97139001 FOREIGN KEY (applicant_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room_user ADD CONSTRAINT FK_EE973C2DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room_user ADD CONSTRAINT FK_EE973C2D54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD friend_code VARCHAR(40) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_FRIEND_CODE ON user (friend_code)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user RENAME INDEX uniq_8d93d64986cc499d TO UNIQ_PSEUDO
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45F97139001
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45FE92F8F78
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F54177093
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room_user DROP FOREIGN KEY FK_EE973C2DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room_user DROP FOREIGN KEY FK_EE973C2D54177093
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE friendship
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE message
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE room
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE room_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_FRIEND_CODE ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP friend_code
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user RENAME INDEX uniq_pseudo TO UNIQ_8D93D64986CC499D
        SQL);
    }
}
