<?php

namespace Matecat\Dqf\Tests;

use Matecat\Dqf\Client;
use Matecat\Dqf\Exceptions\MissingParamsException;
use Matecat\Dqf\Repository\PDODqfUserRepository;
use Matecat\Dqf\SessionProvider;
use Ramsey\Uuid\Uuid;

class ClientTest extends \PHPUnit_Framework_TestCase {
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

    protected function setUp() {
        parent::setUp();

        $this->config = parse_ini_file( __DIR__ . '/../config/parameters.ini', true );
        $this->client = new Client( [
                'apiKey'         => $this->config[ 'dqf' ][ 'API_KEY' ],
                'idPrefix'       => $this->config[ 'dqf' ][ 'ID_PREFIX' ],
                'encryptionKey'  => $this->config[ 'dqf' ][ 'ENCRYPTION_KEY' ],
                'encryptionIV'   => $this->config[ 'dqf' ][ 'ENCRYPTION_IV' ],
                'debug'          => true,
                'logStoragePath' => __DIR__ . '/../log/api.log'
        ] );

        $pdo  = new \PDO( "mysql:host=" . $this->config[ 'pdo' ][ 'SERVER' ] . ";dbname=" . $this->config[ 'pdo' ][ 'DBNAME' ], $this->config[ 'pdo' ][ 'USERNAME' ], $this->config[ 'pdo' ][ 'PASSWORD' ] );
        $repo = new PDODqfUserRepository( $pdo );

        $this->sessionProvider = new SessionProvider( $this->client, $repo );
        $this->sessionId       = $this->sessionProvider->getByCredentials( $this->config[ 'dqf' ][ 'EXTERNAL_ID' ], $this->config[ 'dqf' ][ 'USERNAME' ], $this->config[ 'dqf' ][ 'PASSWORD' ] );
    }

//    protected function tearDown() {
//        parent::tearDown();
//        $this->sessionProvider->destroy($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);
//    }

    /**
     * @test
     */
    public function throw_exception_for_missing_params() {
        try {
            $this->client->login( [
                    'email' => $this->config[ 'dqf' ][ 'USERNAME' ],
            ] );
        } catch ( MissingParamsException $exception ) {
            $this->assertEquals( 'login cannot be executed, wrong or missing params. Required params are: [password]', $exception->getMessage() );
        }
    }

    /**
     * @test
     */
    public function can_login_and_logout() {
        $login = $this->client->login( [
                'email'    => $this->config[ 'dqf' ][ 'USERNAME' ],
                'password' => $this->config[ 'dqf' ][ 'PASSWORD' ],
        ] );

        $this->assertInternalType( 'string', $login->sessionId );

        $logout = $this->client->logout( [
                'email'     => $this->config[ 'dqf' ][ 'USERNAME' ],
                'sessionId' => $login->sessionId,
        ] );

        $this->assertEquals( $logout->message, "Session succesfully removed" );
    }

    /**
     * @test
     * @throws \Matecat\Dqf\Exceptions\SessionProviderException
     */
    public function can_create_retrieve_and_delete_a_master_project() {
        // create
        $masterProject = $this->client->createMasterProject( [
                'sessionId'          => $this->sessionId,
                'name'               => 'test',
                'sourceLanguageCode' => 'it-IT',
                'contentTypeId'      => 1,
                'industryId'         => 1,
                'processId'          => 1,
                'qualityLevelId'     => 1,
        ] );

        $this->assertInstanceOf( \stdClass::class, $masterProject );
        $this->assertInternalType( 'int', $masterProject->dqfId );
        $this->assertInternalType( 'string', $masterProject->dqfUUID );

        // retrieve
        $retrievedMasterProject = $this->client->getMasterProject( [
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProject->dqfUUID,
                'projectId'  => $masterProject->dqfId,
        ] );

        $this->assertInstanceOf( \stdClass::class, $retrievedMasterProject );
        $this->assertInternalType( 'string', $retrievedMasterProject->message );
        $this->assertEquals( 'Project successfully fetched', $retrievedMasterProject->message );

        // delete
        $deleteMasterProject = $this->client->deleteMasterProject( [
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProject->dqfUUID,
                'projectId'  => $masterProject->dqfId,
        ] );

        $this->assertInstanceOf( \stdClass::class, $deleteMasterProject );
        $this->assertInternalType( 'string', $deleteMasterProject->status );
        $this->assertEquals( 'OK', $deleteMasterProject->status );
        $this->assertInternalType( 'string', $deleteMasterProject->message );
        $this->assertEquals( 'Project successfully deleted. Also removed 0 segment mappings, 0 source segments, 0 file/targetLang associations, 0 file mappings, 0 files, 0 target languages, 0 project mappings, 0 review settings, 0 review cycle headers.', $deleteMasterProject->message );
    }

    /**
     * @test
     */
    public function can_create_retrieve_and_delete_a_master_project_file() {
        // create a master project
        $masterProject = $this->client->createMasterProject( [
                'sessionId'          => $this->sessionId,
                'name'               => 'test',
                'sourceLanguageCode' => 'it-IT',
                'contentTypeId'      => 1,
                'industryId'         => 1,
                'processId'          => 1,
                'qualityLevelId'     => 1,
        ] );

        // add a file
        $masterProjectFile = $this->client->addMasterProjectFile( [
                'sessionId'        => $this->sessionId,
                'projectKey'       => $masterProject->dqfUUID,
                'projectId'        => $masterProject->dqfId,
                'name'             => 'test-file',
                'numberOfSegments' => 2,
        ] );

        $this->assertInstanceOf( \stdClass::class, $masterProjectFile );
        $this->assertInternalType( 'int', $masterProjectFile->dqfId );
        $this->assertInternalType( 'string', $masterProjectFile->message );
        $this->assertEquals( 'File successfully created', $masterProjectFile->message );

        // retrieve a file
        $retrieveMasterProjectFile = $this->client->getMasterProjectFile( [
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProject->dqfUUID,
                'projectId'  => $masterProject->dqfId,
        ] );

        $this->assertInstanceOf( \stdClass::class, $retrieveMasterProjectFile );
        $this->assertInternalType( 'string', $retrieveMasterProjectFile->message );
        $this->assertEquals( 'Project Files successfully fetched', $retrieveMasterProjectFile->message );

        // add source segments to root
        $sourceSegmentsBatch = $this->client->addSourceSegmentsInBatchToMasterProject( [
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProject->dqfUUID,
                'projectId'  => $masterProject->dqfId,
                'fileId'     => $masterProjectFile->dqfId,
                'body'       => [
                            [
                                    "sourceSegment" => "Aenean fermentum.",
                                    "index"         => 1,
                                    "clientId"      => Uuid::getFactory()->uuid4()
                            ],
                            [
                                    "sourceSegment" => "Fusce lacus purus, aliquet at, feugiat non, pretium quis, lectus.",
                                    "index"         => 2,
                                    "clientId"      => Uuid::getFactory()->uuid4()
                            ]
                    ]
        ] );

        $this->assertInstanceOf( \stdClass::class, $sourceSegmentsBatch );
        $this->assertInternalType( 'string', $sourceSegmentsBatch->message );
        $this->assertEquals( 'Source Segments successfully created (All segments uploaded)', $sourceSegmentsBatch->message );

        // delete the project
        $this->client->deleteMasterProject( [
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProject->dqfUUID,
                'projectId'  => $masterProject->dqfId,
        ] );
    }

    public function can_delete_a_child() {
    }
}
