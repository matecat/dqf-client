<?php

namespace Matecat\Dqf\Tests;

use Matecat\Dqf\Client;
use Matecat\Dqf\Exceptions\ParamsValidatorException;
use Matecat\Dqf\Repository\PDODqfUserRepository;
use Matecat\Dqf\SessionProvider;
use Ramsey\Uuid\Uuid;

class CompleteDQFWorkflowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var SessionProvider
     */
    private $sessionProvider;

    /**
     * @var string
     */
    private $sessionId;

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

        $this->sessionProvider = new SessionProvider($this->client, $repo);
        $this->sessionId       = $this->sessionProvider->createByCredentials($this->config[ 'dqf' ][ 'EXTERNAL_ID' ], $this->config[ 'dqf' ][ 'USERNAME' ], $this->config[ 'dqf' ][ 'PASSWORD' ]);
    }

    /**
     * @test
     */
    public function test_the_complete_workflow()
    {
        // 1. create a project

        // 2. set a file for the project

        // 3. set target language for the file

        // 4. set review settings for the project

        // 5. update source segments

        // 6. create a 'translation' child node

        // 7. set a file for the child node

        // 8. set target language for the file

        // 9. update translations in batch

        // 10. update a single segment translation

        // 11. check the status of child node

        // 12. create a 'revise' child node

        // 13. update revisions in batch

        // 14. update a single segment revision

        // 15. delete master project
    }
}
