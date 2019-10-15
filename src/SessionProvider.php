<?php

namespace Matecat\Dqf;

use Matecat\Dqf\Exceptions\SessionProviderException;
use Matecat\Dqf\Model\Entity\DqfUser;
use Matecat\Dqf\Model\Repository\DqfUserRepositoryInterface;
use Matecat\Dqf\Utils\DataEncryptor;

class SessionProvider
{
    /**
     * @var DqfUserRepositoryInterface
     */
    private $dqfUserRepository;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var DataEncryptor
     */
    private $dataEncryptor;

    /**
     * SessionProvider constructor.
     *
     * @param Client                     $client
     * @param DqfUserRepositoryInterface $dqfUserRepository
     */
    public function __construct(Client $client, DqfUserRepositoryInterface $dqfUserRepository)
    {
        $this->client            = $client;
        $this->dqfUserRepository = $dqfUserRepository;
        $this->dataEncryptor     = new DataEncryptor($this->client->getClientParams()['encryptionKey'], $this->client->getClientParams()['encryptionIV']);
    }

    /**
     * @param string $genericEmail
     * @param string $genericUsername
     * @param string $genericPassword
     *
     * @return mixed
     * @throws SessionProviderException
     */
    public function createAnonymous($genericEmail, $genericUsername, $genericPassword)
    {
        try {
            $login = $this->client->login(
                [
                    'generic_email' => $genericEmail,
                    'username'      => $genericUsername,
                    'password'      => $genericPassword,
                ]
            );
        } catch (\Exception $e) {
            throw new SessionProviderException('Anonymous login to DQF failed.' . $e->getMessage());
        }

        $dqfUser = new DqfUser();
        $dqfUser->setExternalReferenceId($this->dqfUserRepository->getNextGenericExternalId());
        $dqfUser->setUsername($this->dataEncryptor->encrypt($genericUsername));
        $dqfUser->setPassword($this->dataEncryptor->encrypt($genericPassword));
        $dqfUser->setSessionId($login->sessionId);
        $dqfUser->setSessionExpiresAt((int)(strtotime("now") + (int)$login->expires));
        $dqfUser->setIsGeneric(true);
        $dqfUser->setGenericEmail($this->dataEncryptor->encrypt($genericEmail));
        $this->dqfUserRepository->save($dqfUser);

        return $login->sessionId;
    }

    /**
     * @param string $genericEmail
     *
     * @return mixed
     * @throws SessionProviderException
     */
    public function destroyAnonymous($genericEmail)
    {
        try {
            $dqfUser = $this->dqfUserRepository->getByGenericEmail($this->dataEncryptor->encrypt($genericEmail));

            return $this->dqfUserRepository->delete($dqfUser);
        } catch (\Exception $e) {
            throw new SessionProviderException('Logout from DQF failed.' . $e->getMessage());
        }
    }

    /**
     * @param string $genericEmail
     *
     * @return mixed
     * @throws SessionProviderException
     */
    public function getByGenericEmail($genericEmail)
    {
        if (!$this->hasGenericEmail($genericEmail)) {
            throw new SessionProviderException("Generic user with email " . $genericEmail . " does not exists");
        }

        $dqfUser = $this->dqfUserRepository->getByGenericEmail($this->dataEncryptor->encrypt($genericEmail));
        $dqfUser->setUsername($this->dataEncryptor->decrypt($dqfUser->getUsername()));
        $dqfUser->setPassword($this->dataEncryptor->decrypt($dqfUser->getPassword()));

        if ($this->isSessionStillValid((int)$dqfUser->getSessionExpiresAt())) {
            return $dqfUser->getSessionId();
        }

        return $this->createAnonymous($genericEmail, $dqfUser->getUsername(), $dqfUser->getPassword());
    }

    /**
     * @param string $genericEmail
     *
     * @return bool
     */
    public function hasGenericEmail($genericEmail)
    {
        return ($this->dqfUserRepository->getByGenericEmail($this->dataEncryptor->encrypt($genericEmail)) instanceof DqfUser);
    }

    /**
     * @param mixed  $externalReferenceId
     * @param string $username
     * @param string $password
     *
     * @return mixed
     * @throws SessionProviderException
     */
    public function createByCredentials($externalReferenceId, $username, $password)
    {
        try {
            $login = $this->client->login(
                [
                    'username' => $username,
                    'password' => $password,
                ]
            );
        } catch (\Exception $e) {
            throw new SessionProviderException('Login to DQF failed.' . $e->getMessage());
        }

        // save or update DqfUser
        $dqfUser = new DqfUser();
        $dqfUser->setExternalReferenceId($externalReferenceId);
        $dqfUser->setUsername($this->dataEncryptor->encrypt($username));
        $dqfUser->setPassword($this->dataEncryptor->encrypt($password));
        $dqfUser->setSessionId($login->sessionId);
        $dqfUser->setSessionExpiresAt((int)(strtotime("now") + (int)$login->expires));
        $dqfUser->setIsGeneric(false);
        $this->dqfUserRepository->save($dqfUser);

        return $login->sessionId;
    }

    /**
     * @param int $externalReferenceId
     *
     * @return mixed
     * @throws SessionProviderException
     */
    public function destroy($externalReferenceId)
    {
        try {
            $dqfUser = $this->dqfUserRepository->getByExternalId($externalReferenceId);

            $this->client->logout(
                [
                    'sessionId' => $dqfUser->getSessionId(),
                    'username'  => $this->dataEncryptor->decrypt($dqfUser->getUsername()),
                ]
            );

            return $this->dqfUserRepository->delete($dqfUser);
        } catch (\Exception $e) {
            throw new SessionProviderException('Logout from DQF failed.' . $e->getMessage());
        }
    }

    /**
     * @param mixed $externalReferenceId
     *
     * @return mixed|void
     * @throws SessionProviderException
     */
    public function getById($externalReferenceId)
    {
        if (false === $this->hasId($externalReferenceId)) {
            throw new SessionProviderException("User with id " . $externalReferenceId . " does not exists");
        }

        $dqfUser = $this->dqfUserRepository->getByExternalId($externalReferenceId);
        $dqfUser->setUsername($this->dataEncryptor->decrypt($dqfUser->getUsername()));
        $dqfUser->setPassword($this->dataEncryptor->decrypt($dqfUser->getPassword()));

        if ($this->isSessionStillValid((int)$dqfUser->getSessionExpiresAt())) {
            return $dqfUser->getSessionId();
        }

        return $this->createByCredentials($dqfUser->getExternalReferenceId(), $dqfUser->getUsername(), $dqfUser->getPassword());
    }

    /**
     * @param mixed $externalReferenceId
     *
     * @return bool
     */
    public function hasId($externalReferenceId)
    {
        return ($this->dqfUserRepository->getByExternalId($externalReferenceId) instanceof DqfUser);
    }

    /**
     * @param int $dqf_session_expires
     *
     * @return bool
     */
    private function isSessionStillValid($dqf_session_expires)
    {
        return $dqf_session_expires >= strtotime("now");
    }
}
