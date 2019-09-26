<?php

namespace Matecat\Dqf;

use Matecat\Dqf\Exceptions\SessionProviderException;
use Matecat\Dqf\Model\DqfUser;
use Matecat\Dqf\Model\DqfUserRepositoryInterface;

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
     * SessionProvider constructor.
     *
     * @param Client                     $client
     * @param DqfUserRepositoryInterface $dqfUserRepository
     */
    public function __construct(Client $client, DqfUserRepositoryInterface $dqfUserRepository)
    {
        $this->dqfUserRepository = $dqfUserRepository;
        $this->client            = $client;
    }

    /**
     * @param $genericEmail
     * @param $genericUsername
     * @param $genericPassword
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
        $dqfUser->setExternalReferenceId(Constants::ANONYMOUS_SESSION_ID);
        $dqfUser->setUsername($genericUsername);
        $dqfUser->setPassword($genericPassword);
        $dqfUser->setSessionId($login->sessionId);
        $dqfUser->setSessionExpiresAt((int)(strtotime("now") + (int)$login->expires));
        $dqfUser->setIsGeneric(true);
        $dqfUser->setGenericEmail($genericEmail);
        $this->dqfUserRepository->save($dqfUser);

        return $login->sessionId;
    }

    /**
     * @param int $externalReferenceId
     *
     * @return mixed
     * @throws SessionProviderException
     */
    public function destroyAnonymous($genericEmail)
    {
        try {
            $dqfUser = $this->dqfUserRepository->getByGenericEmail($genericEmail);

            return $this->dqfUserRepository->delete($dqfUser);
        } catch (\Exception $e) {
            throw new SessionProviderException('Logout from DQF failed.' . $e->getMessage());
        }
    }

    /**
     * @param $email
     *
     * @return mixed
     * @throws SessionProviderException
     */
    public function getByGenericEmail($email)
    {
        $dqfUser = $this->dqfUserRepository->getByGenericEmail($email);

        if (!$dqfUser) {
            throw new SessionProviderException("Generic user with email " . $email . " does not exists");
        }

        if ($this->isSessionStillValid((int)$dqfUser->getSessionExpiresAt())) {
            return $dqfUser->getSessionId();
        }

        return $this->createAnonymous($email, $dqfUser->getUsername(), $dqfUser->getPassword());
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
        $dqfUser->setUsername($username);
        $dqfUser->setPassword($password);
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
                            'username'  => $dqfUser->getUsername(),
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
        if ($externalReferenceId === Constants::ANONYMOUS_SESSION_ID) {
            exit;
        }

        $dqfUser = $this->dqfUserRepository->getByExternalId($externalReferenceId);

        if (!$dqfUser) {
            throw new SessionProviderException("User with id " . $externalReferenceId . " does not exists");
        }

        if ($this->isSessionStillValid((int)$dqfUser->getSessionExpiresAt())) {
            return $dqfUser->getSessionId();
        }

        return $this->createByCredentials($dqfUser->getExternalReferenceId(), $dqfUser->getUsername(), $dqfUser->getPassword());
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
