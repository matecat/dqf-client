<?php

namespace Matecat\Dqf\Model;

class DqfUser
{
    private $externalReferenceId;
    private $username;
    private $password;
    private $sessionId;
    private $sessionExpiresAt;

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
}
