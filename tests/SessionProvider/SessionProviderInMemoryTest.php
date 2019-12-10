<?php

namespace Matecat\Dqf\Tests\SessionProvider;

use Matecat\Dqf\Client;
use Matecat\Dqf\Repository\Persistence\InMemoryDqfUserRepository;
use Matecat\Dqf\SessionProvider;

class SessionProviderInMemoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var SessionProvider
     */
    private $sessionProvider;

    protected function setUp()
    {
        parent::setUp();

        $this->config = parse_ini_file(__DIR__ . '/../../config/parameters.ini', true);
        $client       = new Client([
                'apiKey'         => $this->config[ 'dqf' ][ 'API_KEY' ],
                'idPrefix'       => $this->config[ 'dqf' ][ 'ID_PREFIX' ],
                'encryptionKey'  => $this->config[ 'dqf' ][ 'ENCRYPTION_KEY' ],
                'encryptionIV'   => $this->config[ 'dqf' ][ 'ENCRYPTION_IV' ],
                'debug'          => true,
                'logStoragePath' => __DIR__ . '/../../log/api.log'
        ]);

        $repo = new InMemoryDqfUserRepository();

        $this->sessionProvider = new SessionProvider($client, $repo);
    }

    /**
     * @test
     * @throws \Matecat\Dqf\Exceptions\SessionProviderException
     */
    public function can_create_update_and_destroy_anonymous_sessions()
    {
        $email = 'mauro@translated.net';
        $sessionId = $this->sessionProvider->create([
            'username' => $this->config[ 'dqf' ][ 'DQF_GENERIC_USERNAME' ],
            'password' => $this->config[ 'dqf' ][ 'DQF_GENERIC_PASSWORD' ],
            'isGeneric' => true,
            'genericEmail' => $email,
        ]);

        $this->assertInternalType('string', $sessionId);

        $sessionId = $this->sessionProvider->getByGenericEmail($email);
        $this->assertInternalType('string', $sessionId);

        $sessionId = $this->sessionProvider->getByUsername($this->config[ 'dqf' ][ 'DQF_GENERIC_USERNAME' ]);
        $this->assertInternalType('string', $sessionId);

        $destroy = $this->sessionProvider->destroyAnonymous($email);
        $this->assertEquals($destroy, 1);
    }

    /**
     * @test
     * @throws \Matecat\Dqf\Exceptions\SessionProviderException
     */
    public function can_create_update_and_destroy_sessions()
    {
        $sessionId = $this->sessionProvider->create([
            'externalReferenceId' => $this->config[ 'dqf' ][ 'EXTERNAL_ID' ],
            'username'            => $this->config[ 'dqf' ][ 'USERNAME' ],
            'password'            => $this->config[ 'dqf' ][ 'PASSWORD' ],
        ]);
        $this->assertInternalType('string', $sessionId);

        $sessionId = $this->sessionProvider->getById($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);
        $this->assertInternalType('string', $sessionId);

        $sessionId = $this->sessionProvider->getByUsername($this->config[ 'dqf' ][ 'USERNAME' ]);
        $this->assertInternalType('string', $sessionId);

        $destroy = $this->sessionProvider->destroy($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);
        $this->assertEquals($destroy, 1);
    }
}
