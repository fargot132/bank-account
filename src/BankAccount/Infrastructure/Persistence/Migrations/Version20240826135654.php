<?php

declare(strict_types=1);

namespace App\BankAccount\Infrastructure\Persistence\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240826135654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add account and account_transaction tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE account (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', '
            . 'currency VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', '
            . 'updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', balance_value INT NOT NULL, '
            . 'fee_percent_value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) '
            . 'DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE account_transaction (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', '
            . 'account_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', type VARCHAR(255) NOT NULL, '
            . 'currency VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', '
            . 'amount_value INT NOT NULL, fee_value INT NOT NULL, INDEX IDX_723705D19B6B5FBA (account_id), '
            . 'PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE account_transaction ADD CONSTRAINT FK_723705D19B6B5FBA '
            . 'FOREIGN KEY (account_id) REFERENCES account (id)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE account_transaction DROP FOREIGN KEY FK_723705D19B6B5FBA');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE account_transaction');
    }
}
