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
                        'email'     => $dqfUser->getUsername(),
                ]
            );

            return $this->dqfUserRepository->delete($dqfUser);
        } catch (\Exception $e) {
            throw new SessionProviderException('Logout to DQF failed.');
        }
    }

    /**
     * @param mixed $externalReferenceId
     * @param string $username
     * @param string $password
     *
     * @return mixed
     * @throws SessionProviderException
     */
    public function getByCredentials($externalReferenceId, $username, $password)
    {
        try {
            $login = $this->client->login(
                [
                            'email'    => $username,
                            'password' => $password,
                    ]
            );
        } catch (\Exception $e) {
            echo $e->getMessage();
            throw new SessionProviderException('Login to DQF failed.');
        }

        // save or update DqfUser
        $dqfUser = new DqfUser();
        $dqfUser->setExternalReferenceId($externalReferenceId);
        $dqfUser->setUsername($username);
        $dqfUser->setPassword($password);
        $dqfUser->setSessionId($login->sessionId);
        $dqfUser->setSessionExpiresAt((int)(strtotime("now") + (int)$login->expires));
        $this->dqfUserRepository->save($dqfUser);

        return $login->sessionId;
    }

    /**
     * @param mixed $externalReferenceId
     *
     * @return mixed|void
     * @throws SessionProviderException
     */
    public function getById($externalReferenceId)
    {
        $dqfUser = $this->dqfUserRepository->getByExternalId($externalReferenceId);

        if (!$dqfUser) {
            throw new SessionProviderException("User with id " . $externalReferenceId . " does not exists");
        }

        if ($this->isSessionStillValid((int)$dqfUser->getSessionExpiresAt())) {
            return $dqfUser->getSessionId();
        }

        return $this->getByCredentials($dqfUser->getExternalReferenceId(), $dqfUser->getUsername(), $dqfUser->getPassword());
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
