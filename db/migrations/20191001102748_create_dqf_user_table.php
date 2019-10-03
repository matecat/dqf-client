<?php

use Phinx\Migration\AbstractMigration;

class CreateDqfUserTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $sql = 'CREATE TABLE `dqf_user` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `externalReferenceId` int(11) NOT NULL,
              `username` varchar(45) NOT NULL,
              `password` varchar(45) NOT NULL,
              `sessionId` varchar(45) NOT NULL,
              `sessionExpiresAt` int(11) DEFAULT NULL,
              `isGeneric` tinyint(4) DEFAULT NULL,
              `genericEmail` varchar(45) DEFAULT NULL,
              PRIMARY KEY (`id`,`externalReferenceId`),
              UNIQUE KEY `externalReferenceId_UNIQUE` (`externalReferenceId`),
              UNIQUE KEY `genericEmail_UNIQUE` (`genericEmail`)
            ) ENGINE=InnoDB AUTO_INCREMENT=472 DEFAULT CHARSET=latin1';
        $this->execute($sql);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $sql = 'DROP TABLE `dqf_user`;';
        $this->execute($sql);
    }
}
