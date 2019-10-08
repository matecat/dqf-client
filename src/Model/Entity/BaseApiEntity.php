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
     * @param int $dqfId
     */
    public function setDqfId($dqfId)
    {
        $this->dqfId = $dqfId;
    }

    /**
     * @return string
     */
    public function getDqfUuid()
    {
        return $this->dqfUuid;
    }

    /**
     * @param string $dqfUuid
     */
    public function setDqfUuid($dqfUuid)
    {
        $this->dqfUuid = $dqfUuid;
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
