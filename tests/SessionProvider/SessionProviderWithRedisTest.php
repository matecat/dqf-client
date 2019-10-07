<?php

namespace Matecat\Dqf\Tests\SessionProvider;

use Matecat\Dqf\Client;
use Matecat\Dqf\Repository\Persistence\RedisDqfUserRepository;
use Matecat\Dqf\SessionProvider;

class SessionProviderWithRedisTest extends \PHPUnit_Framework_TestCase
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

        $redis  = new \Predis\Client();
        $repo = new RedisDqfUserRepository($redis);

        $this->sessionProvider = new SessionProvider($client, $repo);
    }

    /**
     * @test
     * @throws \Matecat\Dqf\Exceptions\SessionProviderException
     */
    public function can_create_update_and_destroy_anonymous_sessions()
    {
        $email = 'mauro@translated.net';

        $sessionId = $this->sessionProvider->createAnonymous($email, $this->config[ 'dqf' ][ 'DQF_GENERIC_USERNAME' ], $this->config[ 'dqf' ][ 'DQF_GENERIC_PASSWORD' ]);
        $this->assertInternalType('string', $sessionId);

        $sessionId = $this->sessionProvider->getByGenericEmail($email);
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
        $sessionId = $this->sessionProvider->createByCredentials($this->config[ 'dqf' ][ 'EXTERNAL_ID' ], $this->config[ 'dqf' ][ 'USERNAME' ], $this->config[ 'dqf' ][ 'PASSWORD' ]);
        $this->assertInternalType('string', $sessionId);

        $sessionId = $this->sessionProvider->getById($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);
        $this->assertInternalType('string', $sessionId);

        $destroy = $this->sessionProvider->destroy($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);
        $this->assertEquals($destroy, 1);
    }
}
