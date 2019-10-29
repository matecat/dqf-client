<?php

namespace Matecat\Dqf\Tests;

use Matecat\Dqf\Client;
use Matecat\Dqf\Repository\Persistence\PDODqfUserRepository;
use Matecat\Dqf\SessionProvider;
use Ramsey\Uuid\Uuid;

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
     * @var array
     */
    protected $sourceFile;

    /**
     * @var array
     */
    protected $targetFile;

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

        $this->sessionId        = $this->sessionProvider->create([
            'externalReferenceId' => $this->config[ 'dqf' ][ 'EXTERNAL_ID' ],
            'username'            =>  $this->config[ 'dqf' ][ 'USERNAME' ],
            'password'            =>  $this->config[ 'dqf' ][ 'PASSWORD' ]
        ]);

        $this->genericSessionId = $this->sessionProvider->create([
            'genericEmail' =>'mauro@translated.net',
            'username'     =>$this->config[ 'dqf' ][ 'DQF_GENERIC_USERNAME' ],
            'password'     =>$this->config[ 'dqf' ][ 'DQF_GENERIC_PASSWORD' ],
            'isGeneric'    => true,
        ]);

        $this->sourceFile = $this->getSourceFile();
        $this->targetFile = $this->getTranslationFile();
    }

    /**
     * This array represents an hypothetical source file
     *
     * @return array
     * @throws \Exception
     */
    protected function getSourceFile()
    {
        return [
                'uuid'     => Uuid::uuid4()->toString(),
                'name'     => 'original-filename',
                'lang'     => 'it-IT',
                'segments' => [
                        [
                                "sourceSegment" => "La rana in Spagna",
                                "index"         => 1,
                                "clientId"      => Uuid::uuid4()->toString()
                        ],
                        [
                                "sourceSegment" => "gracida in campagna.",
                                "index"         => 2,
                                "clientId"      => Uuid::uuid4()->toString()
                        ],
                        [
                                "sourceSegment" => "Questo è solo uno scioglilingua",
                                "index"         => 3,
                                "clientId"      => Uuid::uuid4()->toString()
                        ]
                ]
        ];
    }

    /**
     * This represents an hypothetical translated file
     *
     * @return array
     * @throws \Exception
     */
    protected function getTranslationFile()
    {
        return [
                'uuid'         => Uuid::uuid4()->toString(),
                'name'         => 'translated-filename',
                'lang'         => 'en-US',
                'segmentPairs' => [
                        [
                                "sourceSegmentId"   => 1,
                                "clientId"          => Uuid::uuid4()->toString(),
                                "targetSegment"     => "",
                                "editedSegment"     => "The frog in Spain",
                                "time"              => 6582,
                                "segmentOriginId"   => 1,
                                "mtEngineId"        => 22,
                                "mtEngineOtherName" => null,
                                "matchRate"         => 0
                        ],
                        [
                                "sourceSegmentId"   => 2,
                                "clientId"          => Uuid::uuid4()->toString(),
                                "targetSegment"     => "croaks in countryside.",
                                "editedSegment"     => "croaks in countryside matus.",
                                "time"              => 5530,
                                "segmentOriginId"   => 2,
                                "mtEngineId"        => 22,
                                "mtEngineOtherName" => null,
                                "matchRate"         => 100
                        ],
                        [
                                "sourceSegmentId"   => 3,
                                "clientId"          => Uuid::uuid4()->toString(),
                                "targetSegment"     => "This is just a tongue twister",
                                "editedSegment"     => "",
                                "time"              => 63455,
                                "segmentOriginId"   => 3,
                                "mtEngineId"        => 22,
                                "mtEngineOtherName" => null,
                                "matchRate"         => 50
                        ],
                ]
        ];
    }
}
