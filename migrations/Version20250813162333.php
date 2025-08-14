<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250813162333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, association_id INTEGER DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, city VARCHAR(120) DEFAULT NULL, street_number VARCHAR(120) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, is_user_favorite BOOLEAN DEFAULT NULL, CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D4E6F81EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D4E6F81A76ED395 ON address (user_id)');
        $this->addSql('CREATE INDEX IDX_D4E6F81EFB9C8A5 ON address (association_id)');
        $this->addSql('CREATE TABLE application (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, mission_id INTEGER NOT NULL, is_accepted BOOLEAN DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_A45BDDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A45BDDC1BE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A45BDDC1A76ED395 ON application (user_id)');
        $this->addSql('CREATE INDEX IDX_A45BDDC1BE6CAE90 ON application (mission_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_application_user_mission ON application (user_id, mission_id)');
        $this->addSql('CREATE TABLE association (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , is_validated BOOLEAN DEFAULT NULL, rna_number VARCHAR(100) DEFAULT NULL, logo_url VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_FD8521CC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FD8521CC7E3C61F9 ON association (owner_id)');
        $this->addSql('CREATE TABLE domain (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(120) NOT NULL, description VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX uniq_domain_name ON domain (name)');
        $this->addSql('CREATE TABLE message (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, sender_id INTEGER NOT NULL, receiver_id INTEGER NOT NULL, content CLOB NOT NULL, sent_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B6BD307FF624B39D ON message (sender_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FCD53EDB6 ON message (receiver_id)');
        $this->addSql('CREATE TABLE mission (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, association_id INTEGER NOT NULL, address_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, volunteer_needed INTEGER NOT NULL, CONSTRAINT FK_9067F23CEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9067F23CF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9067F23CEFB9C8A5 ON mission (association_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9067F23CF5B7AF75 ON mission (address_id)');
        $this->addSql('CREATE TABLE mission_domain (mission_id INTEGER NOT NULL, domain_id INTEGER NOT NULL, PRIMARY KEY(mission_id, domain_id), CONSTRAINT FK_FF51C445BE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_FF51C445115F0EE5 FOREIGN KEY (domain_id) REFERENCES domain (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FF51C445BE6CAE90 ON mission_domain (mission_id)');
        $this->addSql('CREATE INDEX IDX_FF51C445115F0EE5 ON mission_domain (domain_id)');
        $this->addSql('CREATE TABLE notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, message CLOB NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , is_read BOOLEAN NOT NULL, CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)');
        $this->addSql('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, association_id INTEGER NOT NULL, title VARCHAR(180) NOT NULL, content CLOB DEFAULT NULL, posted_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , image_url VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_5A8A6C8DEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DEFB9C8A5 ON post (association_id)');
        $this->addSql('CREATE TABLE report (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reporter_id INTEGER NOT NULL, target_type VARCHAR(50) NOT NULL, target_id INTEGER NOT NULL, description CLOB DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_C42F7784E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C42F7784E1CFE6F5 ON report (reporter_id)');
        $this->addSql('CREATE TABLE role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX uniq_role_name ON role (name)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, first_name VARCHAR(50) DEFAULT NULL, last_name VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , is_available BOOLEAN DEFAULT NULL, avatar_url VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX uniq_user_email ON user (email)');
        $this->addSql('CREATE TABLE user_role (user_id INTEGER NOT NULL, role_id INTEGER NOT NULL, PRIMARY KEY(user_id, role_id), CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3A76ED395 ON user_role (user_id)');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3D60322AC ON user_role (role_id)');
        $this->addSql('CREATE TABLE user_fav_association (user_id INTEGER NOT NULL, association_id INTEGER NOT NULL, PRIMARY KEY(user_id, association_id), CONSTRAINT FK_76E56622A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_76E56622EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_76E56622A76ED395 ON user_fav_association (user_id)');
        $this->addSql('CREATE INDEX IDX_76E56622EFB9C8A5 ON user_fav_association (association_id)');
        $this->addSql('CREATE TABLE user_fav_mission (user_id INTEGER NOT NULL, mission_id INTEGER NOT NULL, PRIMARY KEY(user_id, mission_id), CONSTRAINT FK_466C3D3BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_466C3D3BBE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_466C3D3BA76ED395 ON user_fav_mission (user_id)');
        $this->addSql('CREATE INDEX IDX_466C3D3BBE6CAE90 ON user_fav_mission (mission_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE association');
        $this->addSql('DROP TABLE domain');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE mission');
        $this->addSql('DROP TABLE mission_domain');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE user_fav_association');
        $this->addSql('DROP TABLE user_fav_mission');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
