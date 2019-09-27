<?php

namespace Matecat\Dqf\Tests;

use Matecat\Dqf\Client;
use Matecat\Dqf\Exceptions\ParamsValidatorException;
use Matecat\Dqf\Repository\PDODqfUserRepository;
use Matecat\Dqf\SessionProvider;
use Ramsey\Uuid\Uuid;

class ClientTest extends \PHPUnit_Framework_TestCase
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

//    protected function tearDown() {
//        parent::tearDown();
//        $this->sessionProvider->destroy($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);
//    }

    /**
     * @test
     */
    public function throw_exception_for_missing_params()
    {
        try {
            $this->client->login([
                    'username' => $this->config[ 'dqf' ][ 'USERNAME' ],
            ]);
        } catch (ParamsValidatorException $exception) {
            $this->assertEquals('login cannot be executed. \'password\' param is missing.', $exception->getMessage());
        }
    }

    /**
     * @test
     */
    public function get_basic_attributes_aggregate()
    {
        //getBasicAttributesAggregate
        $basicAttrs = $this->client->getBasicAttributesAggregate([]);

        $this->assertArrayHasKey('language', $basicAttrs);
        $this->assertArrayHasKey('severity', $basicAttrs);
        $this->assertArrayHasKey('mtEngine', $basicAttrs);
        $this->assertArrayHasKey('process', $basicAttrs);
        $this->assertArrayHasKey('contentType', $basicAttrs);
        $this->assertArrayHasKey('segmentOrigin', $basicAttrs);
        $this->assertArrayHasKey('catTool', $basicAttrs);
        $this->assertArrayHasKey('industry', $basicAttrs);
        $this->assertArrayHasKey('errorCategory', $basicAttrs);
        $this->assertArrayHasKey('qualitylevel', $basicAttrs);
    }

    /**
     * @test
     */
    public function can_check_a_LanguageCode()
    {
        $languageCode = $this->client->checkLanguageCode([
                'languageCode'     => 'fr-FR',
        ]);

        $this->assertInstanceOf(\stdClass::class, $languageCode);
        $this->assertEquals('OK', $languageCode->status);
        $this->assertEquals('Valid Language Code', $languageCode->message);
    }

    /**
     * @test
     */
    public function can_login_and_logout()
    {
        $login = $this->client->login([
                'username'    => $this->config[ 'dqf' ][ 'USERNAME' ],
                'password' => $this->config[ 'dqf' ][ 'PASSWORD' ],
        ]);

        $this->assertInternalType('string', $login->sessionId);

        $logout = $this->client->logout([
                'username'     => $this->config[ 'dqf' ][ 'USERNAME' ],
                'sessionId' => $login->sessionId,
        ]);

        $this->assertEquals($logout->message, "Session succesfully removed");
    }

    /**
     * @test
     * throws \Matecat\Dqf\Exceptions\SessionProviderException
     */
    public function can_create_retrieve_and_delete_a_master_project()
    {
        $clientId = Uuid::uuid4()->toString();

        // create
        $masterProject = $this->client->createMasterProject([
                'sessionId'          => $this->sessionId,
                'name'               => 'test',
                'sourceLanguageCode' => 'it-IT',
                'contentTypeId'      => 1,
                'industryId'         => 1,
                'processId'          => 1,
                'qualityLevelId'     => 1,
                'clientId'           => $clientId,
        ]);

        $this->assertInstanceOf(\stdClass::class, $masterProject);
        $this->assertInternalType('int', $masterProject->dqfId);
        $this->assertInternalType('string', $masterProject->dqfUUID);

        // check for DQF ProjectId
        $check = $this->client->getProjectId([
                'sessionId'  => $this->sessionId,
                'clientId' => $clientId,
        ]);
        $this->assertInstanceOf(\stdClass::class, $check);
        $this->assertInternalType('int', $check->dqfId);
        $this->assertInternalType('string', $check->dqfUUID);
        $this->assertEquals('DQF id successfully fetched', $check->message);

        // retrieve
        $retrievedMasterProject = $this->client->getMasterProject([
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProject->dqfUUID,
                'projectId'  => $masterProject->dqfId,
        ]);

        $this->assertInstanceOf(\stdClass::class, $retrievedMasterProject);
        $this->assertInternalType('string', $retrievedMasterProject->message);
        $this->assertEquals('Project successfully fetched', $retrievedMasterProject->message);

        // delete
        $deleteMasterProject = $this->client->deleteMasterProject([
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProject->dqfUUID,
                'projectId'  => $masterProject->dqfId,
        ]);

        $this->assertInstanceOf(\stdClass::class, $deleteMasterProject);
        $this->assertInternalType('string', $deleteMasterProject->status);
        $this->assertEquals('OK', $deleteMasterProject->status);
        $this->assertInternalType('string', $deleteMasterProject->message);
        $this->assertEquals('Project successfully deleted. Also removed 0 segment mappings, 0 source segments, 0 file/targetLang associations, 0 file mappings, 0 files, 0 target languages, 1 project mappings, 0 review settings, 0 review cycle headers.', $deleteMasterProject->message);
    }

    /**
     * @test
     */
    public function can_create_retrieve_and_delete_a_master_project_file()
    {
        // create a master project
        $masterProject = $this->client->createMasterProject([
                'sessionId'          => $this->sessionId,
                'name'               => 'test',
                'sourceLanguageCode' => 'it-IT',
                'contentTypeId'      => 1,
                'industryId'         => 1,
                'processId'          => 1,
                'qualityLevelId'     => 1,
        ]);

        // add a file
        $clientId = Uuid::uuid4()->toString();
        $masterProjectFile = $this->client->addMasterProjectFile([
                'sessionId'        => $this->sessionId,
                'projectKey'       => $masterProject->dqfUUID,
                'projectId'        => $masterProject->dqfId,
                'name'             => 'test-file',
                'numberOfSegments' => 2,
                'clientId'         => $clientId,
        ]);

        $this->assertInstanceOf(\stdClass::class, $masterProjectFile);
        $this->assertInternalType('int', $masterProjectFile->dqfId);
        $this->assertInternalType('string', $masterProjectFile->message);
        $this->assertEquals('File successfully created', $masterProjectFile->message);

        // check for DQF FileId
        $check = $this->client->getFileId([
                'sessionId'  => $this->sessionId,
                'clientId' => $clientId,
        ]);
        $this->assertInstanceOf(\stdClass::class, $check);
        $this->assertInternalType('int', $check->dqfId);
        $this->assertEquals('DQF id successfully fetched', $check->message);

        // retrieve a file
        $retrieveMasterProjectFile = $this->client->getMasterProjectFile([
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProject->dqfUUID,
                'projectId'  => $masterProject->dqfId,
        ]);

        $this->assertInstanceOf(\stdClass::class, $retrieveMasterProjectFile);
        $this->assertInternalType('string', $retrieveMasterProjectFile->message);
        $this->assertEquals('Project Files successfully fetched', $retrieveMasterProjectFile->message);

        // add source segments to root
        $sourceSegmentsBatch = $this->client->addSourceSegmentsInBatchToMasterProject([
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProject->dqfUUID,
                'projectId'  => $masterProject->dqfId,
                'fileId'     => $masterProjectFile->dqfId,
                'body'       => [
                        [
                                "sourceSegment" => "Aenean fermentum.",
                                "index"         => 1,
                                "clientId"      => Uuid::uuid4()->toString()
                        ],
                        [
                                "sourceSegment" => "Fusce lacus purus, aliquet at, feugiat non, pretium quis, lectus.",
                                "index"         => 2,
                                "clientId"      => Uuid::uuid4()->toString()
                        ]
                ]
        ]);

        $this->assertInstanceOf(\stdClass::class, $sourceSegmentsBatch);
        $this->assertInternalType('string', $sourceSegmentsBatch->message);
        $this->assertEquals('Source Segments successfully created (All segments uploaded)', $sourceSegmentsBatch->message);

        // delete the project
        $this->client->deleteMasterProject([
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProject->dqfUUID,
                'projectId'  => $masterProject->dqfId,
        ]);
    }

    /**
     * @test
     */
    public function can_create_retrieve_and_delete_a_child_project()
    {
        // create a master project
        $masterProject = $this->client->createMasterProject([
                'sessionId'          => $this->sessionId,
                'name'               => 'test',
                'sourceLanguageCode' => 'it-IT',
                'contentTypeId'      => 1,
                'industryId'         => 1,
                'processId'          => 1,
                'qualityLevelId'     => 1,
        ]);

        // add a child project
        $childProject = $this->client->createChildProject([
                'sessionId' => $this->sessionId,
                'parentKey' => $masterProject->dqfUUID,
                'type'      => 'translation',
                'name'      => 'test-child',
                'isDummy'   => true,
        ]);

        $this->assertInstanceOf(\stdClass::class, $childProject);
        $this->assertInternalType('int', $childProject->dqfId);
        $this->assertInternalType('string', $childProject->dqfUUID);
        $this->assertInternalType('string', $childProject->message);
        $this->assertEquals('Project successfully created', $childProject->message);

        // retrieve a child project
        $getChildProject = $this->client->getChildProject([
                'sessionId'  => $this->sessionId,
                'projectKey' => $childProject->dqfUUID,
                'projectId'  => $childProject->dqfId,
        ]);

        $this->assertInstanceOf(\stdClass::class, $getChildProject);
        $this->assertInternalType('string', $getChildProject->message);
        $this->assertEquals('Project successfully fetched', $getChildProject->message);

        // delete a child project
        $deleteChildProject = $this->client->deleteChildProject([
                'sessionId'  => $this->sessionId,
                'projectKey' => $childProject->dqfUUID,
                'projectId'  => $childProject->dqfId,
        ]);

        $this->assertInstanceOf(\stdClass::class, $deleteChildProject);
        $this->assertInternalType('string', $deleteChildProject->status);
        $this->assertEquals('OK', $deleteChildProject->status);
        $this->assertInternalType('string', $deleteChildProject->message);
        $this->assertEquals('Project successfully deleted. Also removed 0 file/targetLang associations, 0 file mappings, 0 target languages, 0 project mappings, 0 review cycle details, 0 review cycle headers 0 target segments, 0 edited segments, 0 review settings.', $deleteChildProject->message);
    }

    /**
     * @test
     * @throws \Matecat\Dqf\Exceptions\SessionProviderException
     */
    public function can_destroy_the_session()
    {
        $session = $this->sessionProvider->destroy($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);

        $this->assertEquals(1, $session);
    }
}
