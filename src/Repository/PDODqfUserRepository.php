<?php

namespace Matecat\Dqf\Repository;

use Matecat\Dqf\Constants;
use Matecat\Dqf\Model\DqfUser;
use Matecat\Dqf\Model\DqfUserRepositoryInterface;

class PDODqfUserRepository implements DqfUserRepositoryInterface
{
    const TABLE_NAME = 'dqf_user';

    /**
     * @var \PDO
     */
    private $conn;

    /**
     * PDODqfUserRepository constructor.
     *
     * @param \PDO $conn
     */
    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param int $id
     *
     * @return DqfUser
     */
    public function getByExternalId($id)
    {
        $sql  = "SELECT * FROM " . self::TABLE_NAME . " WHERE externalReferenceId = :externalReferenceId";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
                'externalReferenceId' => $id
        ]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, DqfUser::class);

        return $stmt->fetch();
    }

    /**
     * @param string $genericEmail
     *
     * @return DqfUser|mixed
     */
    public function getByGenericEmail($genericEmail)
    {
        $sql  = "SELECT * FROM " . self::TABLE_NAME . " WHERE 
            externalReferenceId = :externalReferenceId AND 
            genericEmail = :genericEmail AND 
            isGeneric = :isGeneric";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
                'genericEmail'        => $genericEmail,
                'isGeneric'           => true,
                'externalReferenceId' => Constants::ANONYMOUS_SESSION_ID,
        ]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, DqfUser::class);

        return $stmt->fetch();
    }

    /**
     * @param DqfUser $dqfUser
     *
     * @return int|mixed
     */
    public function save(DqfUser $dqfUser)
    {
        $sql = "INSERT IGNORE INTO " . self::TABLE_NAME . " (
                `externalReferenceId`,
                `username`,
                `password`,
                `sessionId`,
                `sessionExpiresAt`,
                `isGeneric`,
                `genericEmail`
            ) VALUES (
                :externalReferenceId,
                :username,
                :password,
                :sessionId,
                :sessionExpiresAt,
                :isGeneric,
                :genericEmail
        )
        ON DUPLICATE KEY UPDATE 
            externalReferenceId = :externalReferenceId,
            username = :username,
            password = :password,
            sessionId = :sessionId,
            sessionExpiresAt = :sessionExpiresAt,
            isGeneric = :isGeneric,
            genericEmail = :genericEmail
        ";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                    'externalReferenceId' => $dqfUser->getExternalReferenceId(),
                    'username'            => $dqfUser->getUsername(),
                    'password'            => $dqfUser->getPassword(),
                    'sessionId'           => $dqfUser->getSessionId(),
                    'sessionExpiresAt'    => $dqfUser->getSessionExpiresAt(),
                    'isGeneric'           => $dqfUser->isGeneric(),
                    'genericEmail'        => $dqfUser->getGenericEmail()
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $stmt->rowCount();
    }

    /**
     * @param DqfUser $dqfUser
     *
     * @return int
     */
    public function delete(DqfUser $dqfUser)
    {
        $sql  = "DELETE FROM " . self::TABLE_NAME . " WHERE externalReferenceId = :externalReferenceId AND username = :username AND password = :password ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
                'externalReferenceId' => $dqfUser->getExternalReferenceId(),
                'username'            => $dqfUser->getUsername(),
                'password'            => $dqfUser->getPassword()
        ]);

        return $stmt->rowCount();
    }
}
