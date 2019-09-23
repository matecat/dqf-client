<?php

namespace Matecat\Dqf\Tests;

use Matecat\Dqf\Client;
use Matecat\Dqf\Repository\PDODqfUserRepository;
use Matecat\Dqf\SessionProvider;

class SessionProviderTest extends \PHPUnit_Framework_TestCase
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

        $this->config = parse_ini_file(__DIR__ . '/../config/parameters.ini', true);
        $client       = new Client([
                'apiKey'         => $this->config[ 'dqf' ][ 'API_KEY' ],
                'idPrefix'       => $this->config[ 'dqf' ][ 'ID_PREFIX' ],
                'encryptionKey'  => $this->config[ 'dqf' ][ 'ENCRYPTION_KEY' ],
                'encryptionIV'   => $this->config[ 'dqf' ][ 'ENCRYPTION_IV' ],
                'debug'          => true,
                'logStoragePath' => __DIR__ . '/../log/api.log'
        ]);

        $pdo  = new \PDO("mysql:host=" . $this->config[ 'pdo' ][ 'SERVER' ] . ";dbname=" . $this->config[ 'pdo' ][ 'DBNAME' ], $this->config[ 'pdo' ][ 'USERNAME' ], $this->config[ 'pdo' ][ 'PASSWORD' ]);
        $repo = new PDODqfUserRepository($pdo);

        $this->sessionProvider = new SessionProvider($client, $repo);
    }

    /**
     * @test
     */
    public function can_create_update_and_destroy_sessions()
    {
        $sessionId = $this->sessionProvider->getByCredentials($this->config[ 'dqf' ][ 'EXTERNAL_ID' ], $this->config[ 'dqf' ][ 'USERNAME' ], $this->config[ 'dqf' ][ 'PASSWORD' ]);
        $this->assertInternalType('string', $sessionId);

        $sessionId = $this->sessionProvider->getById($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);
        $this->assertInternalType('string', $sessionId);

        $destroy = $this->sessionProvider->destroy($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);
        $this->assertEquals($destroy, 1);
    }
}
