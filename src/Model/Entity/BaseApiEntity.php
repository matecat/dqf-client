<?php

namespace Matecat\Dqf\Model\Entity;

abstract class BaseApiEntity
{
    /**
     * @var int
     */
    protected $dqfId;

    /**
     * @var string
     */
    protected $dqfUuid;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @return int
     */
    public function getDqfId()
    {
        return $this->dqfId;
    }

    /**
     * @return string
     */
    public function getDqfUuid()
    {
        return $this->dqfUuid;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }
}
