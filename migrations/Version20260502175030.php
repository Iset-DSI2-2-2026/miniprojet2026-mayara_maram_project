<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260502175030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livre ADD ajoute_par_id INT NOT NULL');
        $this->addSql('ALTER TABLE livre ADD CONSTRAINT FK_AC634F99DAA76F43 FOREIGN KEY (ajoute_par_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AC634F99DAA76F43 ON livre (ajoute_par_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livre DROP FOREIGN KEY FK_AC634F99DAA76F43');
        $this->addSql('DROP INDEX IDX_AC634F99DAA76F43 ON livre');
        $this->addSql('ALTER TABLE livre DROP ajoute_par_id');
    }
}
