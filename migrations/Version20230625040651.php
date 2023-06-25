<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230625040651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_recommandation');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64961AAE789');
        $this->addSql('DROP INDEX FK_recommandation ON user');
        $this->addSql('DROP INDEX IDX_8D93D64961AAE789 ON user');
        $this->addSql('ALTER TABLE user DROP recommandation');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64961AAE789 FOREIGN KEY (recommandation_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64961AAE789 ON user (recommandation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64961AAE789');
        $this->addSql('DROP INDEX IDX_8D93D64961AAE789 ON user');
        $this->addSql('ALTER TABLE user ADD recommandation INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64961AAE789 FOREIGN KEY (recommandation) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX FK_recommandation ON user (recommandation_id)');
        $this->addSql('CREATE INDEX IDX_8D93D64961AAE789 ON user (recommandation)');
    }
}
