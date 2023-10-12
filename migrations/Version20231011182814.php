<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231011182814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Reduces t_answers primary key';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE t_answers DROP CONSTRAINT fk_afc7e651e27f6bf');
        $this->addSql('DROP INDEX idx_afc7e651e27f6bf');
        $this->addSql('ALTER TABLE t_answers DROP CONSTRAINT t_answers_pkey');
        $this->addSql('ALTER TABLE t_answers DROP question_id');
        $this->addSql('ALTER TABLE t_answers ADD PRIMARY KEY (variant_id, attempt_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX t_answers_pkey');
        $this->addSql('ALTER TABLE t_answers ADD question_id INT NOT NULL');
        $this->addSql('ALTER TABLE t_answers ADD CONSTRAINT fk_afc7e651e27f6bf FOREIGN KEY (question_id) REFERENCES t_questions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_afc7e651e27f6bf ON t_answers (question_id)');
        $this->addSql('ALTER TABLE t_answers ADD PRIMARY KEY (question_id, variant_id, attempt_id)');
    }
}
