<?php

namespace Matecat\Dqf\Repository\Api;

use Matecat\Dqf\Client;
use Matecat\Dqf\Model\Repository\CrudApiRepositoryInterface;

abstract class AbstractApiRepository implements CrudApiRepositoryInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var null
     */
    protected $genericEmail;

    /**
     * AbstractApiRepository constructor.
     *
     * @param Client $client
     * @param string $sessionId
     * @param null   $genericEmail
     */
    public function __construct(Client $client, $sessionId, $genericEmail = null)
    {
        $this->client = $client;
        $this->sessionId = $sessionId;
        $this->genericEmail = $genericEmail;
    }
}
