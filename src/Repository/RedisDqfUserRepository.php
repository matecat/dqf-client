<?php

namespace Matecat\Dqf\Repository;

use Matecat\Dqf\Model\DqfUser;
use Matecat\Dqf\Model\DqfUserRepositoryInterface;
use Predis\Client as Redis;

class RedisDqfUserRepository implements DqfUserRepositoryInterface
{
    const DQF_USER_HASHSET =  'DQF_USER_HASHSET';

    /**
     * @var Redis
     */
    private $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param DqfUser $dqfUser
     *
     * @return mixed
     */
    public function delete(DqfUser $dqfUser)
    {
        if ($this->redis->hdel(self::DQF_USER_HASHSET, $dqfUser->getExternalReferenceId())) {
            return 1;
        }

        return 0;
    }

    /**
     * @param int $id
     *
     * @return DqfUser
     */
    public function getByExternalId($id)
    {
        return unserialize($this->redis->hget(self::DQF_USER_HASHSET, $id));
    }

    /**
     * @param string $genericEmail
     *
     * @return DqfUser
     */
    public function getByGenericEmail($genericEmail)
    {
        $users = $this->redis->hgetall(self::DQF_USER_HASHSET);
        foreach ($users as $user) {
            $dqfUser = unserialize($user);
            if ($dqfUser->getGenericEmail() === $genericEmail) {
                return $dqfUser;
            }
        }
    }

    /**
     * @param DqfUser $dqfUser
     *
     * @return mixed
     */
    public function save(DqfUser $dqfUser)
    {
        if ($this->redis->hset(self::DQF_USER_HASHSET, $dqfUser->getExternalReferenceId(), serialize($dqfUser))) {
            return 1;
        }

        return 0;
    }
}
