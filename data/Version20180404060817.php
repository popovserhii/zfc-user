<?php declare(strict_types=1);

namespace DoctrineORMModule\Migrations;

use DateTime;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180404060817 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->connection->insert('user', [
            'email' => 'admin@stagem.com.ua',
            'password' => '84862b7574a0bc370277c63c6d6eaacc', // 123456
            'firstName' => 'Support',
            'lastName' => 'Admin',
            'createdAt' => (new DateTime())->format('Y-m-d H:i:s'),
            'isInner' => 1,
        ]);
        $userId = $this->connection->lastInsertId();
        #$this->abortIf(!$userId, 'Cannot insert admin user');

        $this->connection->insert('role', [
            'name' => 'Admin',
            'mnemo' => 'admin',
            'resource' => 'all',
        ]);
        $roleId = $this->connection->lastInsertId();

        $this->connection->insert('users_roles', [
            'userId' => $userId,
            'roleId' => $roleId,
        ]);
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
