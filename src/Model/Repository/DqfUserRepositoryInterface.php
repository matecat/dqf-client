<?php

namespace Matecat\Dqf\Model\Repository;

use Matecat\Dqf\Model\Entity\DqfUser;

interface DqfUserRepositoryInterface
{
    /**
     * @param DqfUser $dqfUser
     *
     * @return mixed
     */
    public function delete(DqfUser $dqfUser);

    /**
     * @param int $id
     *
     * @return DqfUser
     */
    public function getByExternalId($id);

    /**
     * @param string $username
     *
     * @return DqfUser
     */
    public function getByUsername($username);

    /**
     * @param string $genericEmail
     *
     * @return DqfUser
     */
    public function getByGenericEmail($genericEmail);

    /**
     * @return int
     */
    public function getNextGenericExternalId();

    /**
     * @param DqfUser $dqfUser
     *
     * @return mixed
     */
    public function save(DqfUser $dqfUser);
}
