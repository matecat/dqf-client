<?php

namespace Matecat\Dqf\Repository;

use Matecat\Dqf\Model\DqfUser;
use Matecat\Dqf\Model\DqfUserRepositoryInterface;

class InMemoryDqfUserRepository implements DqfUserRepositoryInterface
{
    public function delete(DqfUser $dqfUser)
    {
        // TODO: Implement delete() method.
    }

    public function getByExternalId($id)
    {
        // TODO: Implement getByExternalId() method.
    }

    public function getByCredentials($username, $password)
    {
        // TODO: Implement getByCredentials() method.
    }

    public function save(DqfUser $dqfUser)
    {
        // TODO: Implement save() method.
    }
}
