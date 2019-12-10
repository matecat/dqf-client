<?php

namespace Matecat\Dqf\Repository\Persistence;

use Matecat\Dqf\Constants;
use Matecat\Dqf\Model\Entity\DqfUser;
use Matecat\Dqf\Model\Repository\DqfUserRepositoryInterface;

class InMemoryDqfUserRepository implements DqfUserRepositoryInterface
{
    /**
     * @var DqfUser[]
     */
    private $users;

    /**
     * @param DqfUser $dqfUser
     *
     * @return DqfUser|mixed
     */
    public function delete(DqfUser $dqfUser)
    {
        foreach ($this->users as $dqfU) {
            if ($dqfU->getGenericEmail() === $dqfUser->getGenericEmail()) {
                unset($this->users[$dqfU->getGenericEmail()]);

                return 1;
            }
        }
    }

    /**
     * @param int $id
     *
     * @return DqfUser
     */
    public function getByExternalId($id)
    {
        return $this->users[$id];
    }

    /**
     * @param string $genericEmail
     *
     * @return DqfUser
     */
    public function getByGenericEmail($genericEmail)
    {
        foreach ($this->users as $dqfUser) {
            if ($dqfUser->getGenericEmail() === $genericEmail) {
                return $dqfUser;
            }
        }
    }

    /**
     * @param string $username
     *
     * @return DqfUser
     */
    public function getByUsername($username)
    {
        foreach ($this->users as $dqfUser) {
            if ($dqfUser->getUsername() === $username) {
                return $dqfUser;
            }
        }
    }

    /**
     * @return int
     */
    public function getNextGenericExternalId()
    {
        if (empty($this->users)) {
            return Constants::ANONYMOUS_SESSION_ID;
        }

        $ids = [];

        foreach ($this->users as $dqfUser) {
            $ids[]  = $dqfUser->getExternalReferenceId();
        }

        sort($ids);

        if ($ids[0] > 0) {
            return Constants::ANONYMOUS_SESSION_ID;
        }

        return $ids[0] - 1;
    }

    /**
     * @param DqfUser $dqfUser
     *
     * @return int|mixed
     */
    public function save(DqfUser $dqfUser)
    {
        $this->users[$dqfUser->getExternalReferenceId()] = $dqfUser;

        return 1;
    }
}
