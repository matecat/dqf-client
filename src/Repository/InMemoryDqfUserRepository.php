<?php

namespace Matecat\Dqf\Repository;

use Matecat\Dqf\Model\DqfUser;
use Matecat\Dqf\Model\DqfUserRepositoryInterface;

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
