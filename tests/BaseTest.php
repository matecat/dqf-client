<?php

namespace Matecat\Dqf\Tests;

use Matecat\Dqf\Client;
use Matecat\Dqf\Repository\Persistence\PDODqfUserRepository;
use Matecat\Dqf\SessionProvider;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var SessionProvider
     */
    protected $sessionProvider;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var string
     */
    protected $genericSessionId;

    /**
     * @var string
     */
    protected $genericEmail;

    /**
     * @throws \Matecat\Dqf\Exceptions\SessionProviderException
     */
    protected function setUp()
    {
        parent::setUp();

        $this->config = parse_ini_file(__DIR__ . '/../config/parameters.ini', true);
        $this->client = new Client([
                'apiKey'         => $this->config[ 'dqf' ][ 'API_KEY' ],
                'idPrefix'       => $this->config[ 'dqf' ][ 'ID_PREFIX' ],
                'encryptionKey'  => $this->config[ 'dqf' ][ 'ENCRYPTION_KEY' ],
                'encryptionIV'   => $this->config[ 'dqf' ][ 'ENCRYPTION_IV' ],
                'debug'          => true,
                'logStoragePath' => __DIR__ . '/../log/api.log'
        ]);

        $pdo  = new \PDO("mysql:host=" . $this->config[ 'pdo' ][ 'SERVER' ] . ";dbname=" . $this->config[ 'pdo' ][ 'DBNAME' ], $this->config[ 'pdo' ][ 'USERNAME' ], $this->config[ 'pdo' ][ 'PASSWORD' ]);
        $repo = new PDODqfUserRepository($pdo);

        $this->genericEmail     = 'mauro@translated.net';
        $this->sessionProvider  = new SessionProvider($this->client, $repo);
        $this->sessionId        = $this->sessionProvider->createByCredentials($this->config[ 'dqf' ][ 'EXTERNAL_ID' ], $this->config[ 'dqf' ][ 'USERNAME' ], $this->config[ 'dqf' ][ 'PASSWORD' ]);
        $this->genericSessionId = $this->sessionProvider->createAnonymous('mauro@translated.net', $this->config[ 'dqf' ][ 'DQF_GENERIC_USERNAME' ], $this->config[ 'dqf' ][ 'DQF_GENERIC_PASSWORD' ]);
    }
}
