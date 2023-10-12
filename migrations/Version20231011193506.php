<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231011193506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds finishedAt date to quiz attempt';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE t_quiz_attempts ADD finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN t_quiz_attempts.finished_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE t_quiz_attempts DROP finished_at');
    }
}
