<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180404060817 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->exec("INSERT INTO `user` (`email`, `password`, `firstName`, `lastName`, `patronymic`, `phone`, `phoneWork`, `phoneInternal`, `post`, `birthedAt`, `employedAt`, `photo`, `notation`, `createdAt`, `isInner`) VALUES ('storage@stagem.com.ua', 'c80f4ae8a82e0033305cae3f1928d1d3', 'Support Stagem', 'Адмім', '', '', '', '', '', NULL, NULL, '', '', '2018-03-16 16:23:40', '1');");
        $userId = $this->connection->lastInsertId();

        #$this->abortIf(!$userId, 'Cannot insert admin user');
        $this->connection->exec("INSERT INTO `role` (`name`, `mnemo`, `resource`) VALUES ('Admin', 'admin', 'all')");
        $roleId = $this->connection->lastInsertId();

        $this->connection->insert('users_roles', [
            'userId' => $userId,
            'roleId' => $roleId,
        ]);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
