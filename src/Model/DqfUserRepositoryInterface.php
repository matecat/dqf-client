<?php

namespace Matecat\Dqf\Model;

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
     * @param string $password
     *
     * @return DqfUser
     */
    public function getByCredentials($username, $password);

    /**
     * @param $genericEmail
     *
     * @return DqfUser
     */
    public function getByGenericEmail($genericEmail);

    /**
     * @param DqfUser $dqfUser
     *
     * @return mixed
     */
    public function save(DqfUser $dqfUser);
}
