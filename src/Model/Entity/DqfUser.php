<?php

namespace Matecat\Dqf\Model\Entity;

class DqfUser
{
    /**
     * @var int
     */
    private $externalReferenceId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $sessionId;

    /**
     * @var int
     */
    private $sessionExpiresAt;

    /**
     * @var bool
     */
    private $isGeneric;

    /**
     * @var string
     */
    private $genericEmail;

    /**
     * @return mixed
     */
    public function getExternalReferenceId()
    {
        return $this->externalReferenceId;
    }

    /**
     * @param mixed $externalReferenceId
     */
    public function setExternalReferenceId($externalReferenceId)
    {
        $this->externalReferenceId = $externalReferenceId;
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getSessionExpiresAt()
    {
        return $this->sessionExpiresAt;
    }

    /**
     * @param mixed $sessionExpiresAt
     */
    public function setSessionExpiresAt($sessionExpiresAt)
    {
        $this->sessionExpiresAt = $sessionExpiresAt;
    }

    /**
     * @return bool
     */
    public function isGeneric()
    {
        return $this->isGeneric;
    }

    /**
     * @param bool $isGeneric
     */
    public function setIsGeneric($isGeneric)
    {
        $this->isGeneric = $isGeneric;
    }

    /**
     * @return string
     */
    public function getGenericEmail()
    {
        return $this->genericEmail;
    }

    /**
     * @param string $genericEmail
     */
    public function setGenericEmail($genericEmail)
    {
        $this->genericEmail = $genericEmail;
    }
}
