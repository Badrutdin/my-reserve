<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250112212346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE employee (id SERIAL NOT NULL, organization_id INT NOT NULL, position_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5D9F75A132C8A3DE ON employee (organization_id)');
        $this->addSql('CREATE INDEX IDX_5D9F75A1DD842E46 ON employee (position_id)');
        $this->addSql('CREATE TABLE organization (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, subdomain VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE organization_users_position (id SERIAL NOT NULL, organization_id INT NOT NULL, user_id INT NOT NULL, position_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CD7F2C9C32C8A3DE ON organization_users_position (organization_id)');
        $this->addSql('CREATE INDEX IDX_CD7F2C9CA76ED395 ON organization_users_position (user_id)');
        $this->addSql('CREATE INDEX IDX_CD7F2C9CDD842E46 ON organization_users_position (position_id)');
        $this->addSql('CREATE UNIQUE INDEX organization_user_position_unique ON organization_users_position (organization_id, user_id, position_id)');
        $this->addSql('CREATE TABLE organization_users_role (id SERIAL NOT NULL, organization_id INT NOT NULL, user_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_71BB568B32C8A3DE ON organization_users_role (organization_id)');
        $this->addSql('CREATE INDEX IDX_71BB568BA76ED395 ON organization_users_role (user_id)');
        $this->addSql('CREATE INDEX IDX_71BB568BD60322AC ON organization_users_role (role_id)');
        $this->addSql('CREATE UNIQUE INDEX organization_user_unique ON organization_users_role (organization_id, user_id)');
        $this->addSql('CREATE TABLE position (id SERIAL NOT NULL, organization_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_462CE4F532C8A3DE ON position (organization_id)');
        $this->addSql('CREATE TABLE role (id SERIAL NOT NULL, organization_id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(10) NOT NULL, is_editable BOOLEAN NOT NULL, is_deletable BOOLEAN NOT NULL, permissions JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_57698A6A32C8A3DE ON role (organization_id)');
        $this->addSql('CREATE TABLE "users" (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON "users" (email)');
        $this->addSql('CREATE TABLE user_organization (user_id INT NOT NULL, organization_id INT NOT NULL, PRIMARY KEY(user_id, organization_id))');
        $this->addSql('CREATE INDEX IDX_41221F7EA76ED395 ON user_organization (user_id)');
        $this->addSql('CREATE INDEX IDX_41221F7E32C8A3DE ON user_organization (organization_id)');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1DD842E46 FOREIGN KEY (position_id) REFERENCES position (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organization_users_position ADD CONSTRAINT FK_CD7F2C9C32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organization_users_position ADD CONSTRAINT FK_CD7F2C9CA76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organization_users_position ADD CONSTRAINT FK_CD7F2C9CDD842E46 FOREIGN KEY (position_id) REFERENCES position (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organization_users_role ADD CONSTRAINT FK_71BB568B32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organization_users_role ADD CONSTRAINT FK_71BB568BA76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organization_users_role ADD CONSTRAINT FK_71BB568BD60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F532C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_organization ADD CONSTRAINT FK_41221F7EA76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_organization ADD CONSTRAINT FK_41221F7E32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT FK_5D9F75A132C8A3DE');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT FK_5D9F75A1DD842E46');
        $this->addSql('ALTER TABLE organization_users_position DROP CONSTRAINT FK_CD7F2C9C32C8A3DE');
        $this->addSql('ALTER TABLE organization_users_position DROP CONSTRAINT FK_CD7F2C9CA76ED395');
        $this->addSql('ALTER TABLE organization_users_position DROP CONSTRAINT FK_CD7F2C9CDD842E46');
        $this->addSql('ALTER TABLE organization_users_role DROP CONSTRAINT FK_71BB568B32C8A3DE');
        $this->addSql('ALTER TABLE organization_users_role DROP CONSTRAINT FK_71BB568BA76ED395');
        $this->addSql('ALTER TABLE organization_users_role DROP CONSTRAINT FK_71BB568BD60322AC');
        $this->addSql('ALTER TABLE position DROP CONSTRAINT FK_462CE4F532C8A3DE');
        $this->addSql('ALTER TABLE role DROP CONSTRAINT FK_57698A6A32C8A3DE');
        $this->addSql('ALTER TABLE user_organization DROP CONSTRAINT FK_41221F7EA76ED395');
        $this->addSql('ALTER TABLE user_organization DROP CONSTRAINT FK_41221F7E32C8A3DE');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE organization_users_position');
        $this->addSql('DROP TABLE organization_users_role');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE "users"');
        $this->addSql('DROP TABLE user_organization');
    }
}
