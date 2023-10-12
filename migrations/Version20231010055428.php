<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231010055428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE t_questions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE t_quiz_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE t_quiz_attempts_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE t_users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE t_variants_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE t_answers (question_id INT NOT NULL, variant_id INT NOT NULL, attempt_id INT NOT NULL, PRIMARY KEY(question_id, variant_id, attempt_id))');
        $this->addSql('CREATE INDEX IDX_AFC7E651E27F6BF ON t_answers (question_id)');
        $this->addSql('CREATE INDEX IDX_AFC7E653B69A9AF ON t_answers (variant_id)');
        $this->addSql('CREATE INDEX IDX_AFC7E65B191BE6B ON t_answers (attempt_id)');
        $this->addSql('CREATE TABLE t_questions (id INT NOT NULL, quiz_id INT DEFAULT NULL, text TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1B60D23853CD175 ON t_questions (quiz_id)');
        $this->addSql('CREATE TABLE t_quiz (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE t_quiz_attempts (id INT NOT NULL, quiz_id INT DEFAULT NULL, user_id INT DEFAULT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4C1B9BE0853CD175 ON t_quiz_attempts (quiz_id)');
        $this->addSql('CREATE INDEX IDX_4C1B9BE0A76ED395 ON t_quiz_attempts (user_id)');
        $this->addSql('COMMENT ON COLUMN t_quiz_attempts.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE t_users (id INT NOT NULL, nickname VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE t_variants (id INT NOT NULL, question_id INT DEFAULT NULL, text TEXT NOT NULL, is_correct BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_67794FBB1E27F6BF ON t_variants (question_id)');
        $this->addSql('ALTER TABLE t_answers ADD CONSTRAINT FK_AFC7E651E27F6BF FOREIGN KEY (question_id) REFERENCES t_questions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE t_answers ADD CONSTRAINT FK_AFC7E653B69A9AF FOREIGN KEY (variant_id) REFERENCES t_variants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE t_answers ADD CONSTRAINT FK_AFC7E65B191BE6B FOREIGN KEY (attempt_id) REFERENCES t_quiz_attempts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE t_questions ADD CONSTRAINT FK_1B60D23853CD175 FOREIGN KEY (quiz_id) REFERENCES t_quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE t_quiz_attempts ADD CONSTRAINT FK_4C1B9BE0853CD175 FOREIGN KEY (quiz_id) REFERENCES t_quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE t_quiz_attempts ADD CONSTRAINT FK_4C1B9BE0A76ED395 FOREIGN KEY (user_id) REFERENCES t_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE t_variants ADD CONSTRAINT FK_67794FBB1E27F6BF FOREIGN KEY (question_id) REFERENCES t_questions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE t_questions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE t_quiz_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE t_quiz_attempts_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE t_users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE t_variants_id_seq CASCADE');
        $this->addSql('ALTER TABLE t_answers DROP CONSTRAINT FK_AFC7E651E27F6BF');
        $this->addSql('ALTER TABLE t_answers DROP CONSTRAINT FK_AFC7E653B69A9AF');
        $this->addSql('ALTER TABLE t_answers DROP CONSTRAINT FK_AFC7E65B191BE6B');
        $this->addSql('ALTER TABLE t_questions DROP CONSTRAINT FK_1B60D23853CD175');
        $this->addSql('ALTER TABLE t_quiz_attempts DROP CONSTRAINT FK_4C1B9BE0853CD175');
        $this->addSql('ALTER TABLE t_quiz_attempts DROP CONSTRAINT FK_4C1B9BE0A76ED395');
        $this->addSql('ALTER TABLE t_variants DROP CONSTRAINT FK_67794FBB1E27F6BF');
        $this->addSql('DROP TABLE t_answers');
        $this->addSql('DROP TABLE t_questions');
        $this->addSql('DROP TABLE t_quiz');
        $this->addSql('DROP TABLE t_quiz_attempts');
        $this->addSql('DROP TABLE t_users');
        $this->addSql('DROP TABLE t_variants');
    }
}
