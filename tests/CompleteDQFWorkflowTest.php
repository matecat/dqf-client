<?php

namespace Matecat\Dqf\Tests;

use Ramsey\Uuid\Uuid;

class CompleteDQFWorkflowTest extends AbstractClientTest {
    /**
     * This array represents an hypothetical source file
     *
     * @return array
     * @throws \Exception
     */
    public function getSourceFile() {
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
    public function getTranslationFile() {
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
                                "targetSegment"     => "",
                                "editedSegment"     => "This is just a tongue twister",
                                "time"              => 63455,
                                "segmentOriginId"   => 3,
                                "mtEngineId"        => 22,
                                "mtEngineOtherName" => null,
                                "matchRate"         => 50
                        ],
                ]
        ];
    }

    /**
     * @throws \Exception
     * @test
     */
    public function test_the_complete_workflow() {

        $sourceFile = $this->getSourceFile();
        $targetFile = $this->getTranslationFile();

        /**
         ****************************************************************************
         * STEP 1. create a project (checking if the sourceLanguageCode is valid first)
         ****************************************************************************
         */
        $languageCode = $this->client->checkLanguageCode( [
                'languageCode' => $sourceFile[ 'lang' ],
        ] );

        $this->assertEquals( 'OK', $languageCode->status );

        $masterProjectClientId = Uuid::uuid4()->toString();
        $masterProject         = $this->client->createMasterProject( [
                'sessionId'          => $this->sessionId,
                'name'               => 'master-workflow-test',
                'sourceLanguageCode' => $sourceFile[ 'lang' ],
                'contentTypeId'      => 1,
                'industryId'         => 1,
                'processId'          => 1,
                'qualityLevelId'     => 1,
                'clientId'           => $masterProjectClientId,
        ] );

        /**
         ****************************************************************************
         * STEP 2. set a file for the project
         ****************************************************************************
         */
        $this->assertNotEmpty( $masterProject->dqfId );
        $this->assertNotEmpty( $masterProject->dqfUUID );

        $masterProjectFile = $this->client->addMasterProjectFile( [
                'sessionId'        => $this->sessionId,
                'projectKey'       => $masterProject->dqfUUID,
                'projectId'        => $masterProject->dqfId,
                'name'             => $sourceFile[ 'name' ],
                'numberOfSegments' => count( $sourceFile[ 'segments' ] ),
                'clientId'         => $sourceFile[ 'uuid' ],
        ] );

        /**
         ****************************************************************************
         * STEP 3. set target language for the file (checking if the lang is valid first)
         ****************************************************************************
         */
        $languageCode = $this->client->checkLanguageCode( [
                'languageCode' => $targetFile[ 'lang' ],
        ] );

        $this->assertEquals( 'OK', $languageCode->status );
        $this->assertNotEmpty( $masterProjectFile->dqfId );

        $masterProjectTargetLang = $this->client->addTargetLanguageToMasterProject( [
                'sessionId'          => $this->sessionId,
                'projectKey'         => $masterProject->dqfUUID,
                'projectId'          => $masterProject->dqfId,
                'fileId'             => $masterProjectFile->dqfId,
                'targetLanguageCode' => $targetFile[ 'lang' ],
        ] );

        /**
         ****************************************************************************
         * STEP 4. set review settings for the project
         ****************************************************************************
         */
        $this->assertNotEmpty( $masterProjectTargetLang->dqfId );

        $masterProjectReview = $this->client->addMasterProjectReviewSettings( [
                'sessionId'           => $this->sessionId,
                'projectKey'          => $masterProject->dqfUUID,
                'projectId'           => $masterProject->dqfId,
                'reviewType'          => 'combined',
                'severityWeights'     => '[{"severityId":"1","weight":1}, {"severityId":"2","weight":2}, {"severityId":"3","weight":3}, {"severityId":"4","weight":4}]',
                'errorCategoryIds[0]' => 9,
                'errorCategoryIds[1]' => 10,
                'errorCategoryIds[2]' => 11,
                'passFailThreshold'   => 1.00,
        ] );

        /**
         ****************************************************************************
         * STEP 5. update source segments in batch
         ****************************************************************************
         */
        $this->assertNotEmpty( $masterProjectReview->dqfId );

        $updatedSourceSegments = $this->client->addSourceSegmentsInBatchToMasterProject( [
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProject->dqfUUID,
                'projectId'  => $masterProject->dqfId,
                'fileId'     => $masterProjectFile->dqfId,
                'body'       => $sourceFile[ 'segments' ]
        ] );

        /**
         ****************************************************************************
         * STEP 6. create a 'translation' child node
         ****************************************************************************
         */
        $this->assertEquals( $updatedSourceSegments->message, "Source Segments successfully created (All segments uploaded)" );

        $childTranslation = $this->client->createChildProject( [
                'sessionId' => $this->sessionId,
                'parentKey' => $masterProject->dqfUUID,
                'type'      => 'translation',
                'name'      => 'child-workflow-test',
                'isDummy'   => true,
        ] );

        /**
         ****************************************************************************
         * STEP 7. set target language for the child node
         ****************************************************************************
         */
        $this->assertNotEmpty( $childTranslation->dqfId );
        $this->assertNotEmpty( $childTranslation->dqfUUID );

        $childTranslationTargetLang = $this->client->addTargetLanguageToChildProject( [
                'sessionId'          => $this->sessionId,
                'projectKey'         => $childTranslation->dqfUUID,
                'projectId'          => $childTranslation->dqfId,
                'fileId'             => $masterProjectFile->dqfId,
                'targetLanguageCode' => $targetFile[ 'lang' ],
        ] );

        /**
         ****************************************************************************
         * STEP 8. update translations in batch
         ****************************************************************************
         */
        $this->assertNotEmpty( $childTranslationTargetLang->dqfId );
        $this->assertEquals( $childTranslationTargetLang->message, "TargetLang successfully created" );

        // update the fake $targetFile with real DQL ids for 'sourceSegmentId'
        $segmentPairs = $targetFile[ 'segmentPairs' ];
        foreach ( $segmentPairs as $key => $segmentPair ) {
            $segmentPairs[ $key ][ 'sourceSegmentId' ] = $this->client->getSegmentId( [
                    'sessionId' => $this->sessionId,
                    'clientId'  => $sourceFile[ 'segments' ][ $key ][ 'clientId' ],
            ] )->dqfId;
        }

        $translationsBatch = $this->client->addTranslationsForSourceSegmentsInBatch( [
                'sessionId'      => $this->sessionId,
                'projectKey'     => $childTranslation->dqfUUID,
                'projectId'      => $childTranslation->dqfId,
                'fileId'         => $masterProjectFile->dqfId,
                'targetLangCode' => $targetFile[ 'lang' ],
                'body'           => $segmentPairs,
        ] );

        $this->assertEquals( $translationsBatch->message, "Translations successfully created" );

        /**
         ****************************************************************************
         * STEP 9. update a single segment translation
         ****************************************************************************
         */
        $firstSegmentId = $this->client->getSegmentId( [
                'sessionId' => $this->sessionId,
                'clientId'  => $sourceFile[ 'segments' ][ 0 ][ 'clientId' ],
        ] );

        $firstTranslationId = $this->client->getTranslationId( [
                'sessionId' => $this->sessionId,
                'clientId'  => $targetFile[ 'segmentPairs' ][ 0 ][ 'clientId' ],
        ] );

        $this->assertNotEmpty( $firstSegmentId->dqfId );

        $updateSingleSegmentTranslation = $this->client->updateTranslationForASegment( [
                'sessionId'       => $this->sessionId,
                'projectKey'      => $childTranslation->dqfUUID,
                'projectId'       => $childTranslation->dqfId,
                'fileId'          => $masterProjectFile->dqfId,
                'targetLangCode'  => $targetFile[ 'lang' ],
                'sourceSegmentId' => $firstSegmentId->dqfId,
                'translationId'   => $firstTranslationId->dqfId,
                'segmentOriginId' => $this->getSegmentOrigin('HT'),
                'targetSegment'   => "",
                'editedSegment'   => "The frog in Spain (Barcelona)",
                'time'            => 5435435,
        ] );

        $this->assertEquals( $updateSingleSegmentTranslation->message, "Segments successfully updated" );

        /**
         ****************************************************************************
         * STEP 10. check the status of child node
         ****************************************************************************
         */
        $childNodeStatus = $this->client->getChildProjectStatus([
                'sessionId'       => $this->sessionId,
                'projectKey'      => $childTranslation->dqfUUID,
                'projectId'       => $childTranslation->dqfId,
        ]);

        $this->assertEquals( $childNodeStatus->status, "OK" );
        $this->assertEquals( $childNodeStatus->message, "inprogress" );

        /**
         ****************************************************************************
         * STEP 11. create a 'review' child node
         ****************************************************************************
         */
        $childReview = $this->client->createChildProject( [
                'sessionId' => $this->sessionId,
                'parentKey' => $masterProject->dqfUUID,
                'type'      => 'review',
                'name'      => 'child-revision-workflow-test',
                'isDummy'   => false,
        ] );

        $this->assertNotEmpty( $childReview->dqfId );
        $this->assertNotEmpty( $childReview->dqfUUID );

        /**
         ****************************************************************************
         * STEP 12. update revisions in batch
         ****************************************************************************
         */

        /**
         ****************************************************************************
         * STEP 13. update a single segment revision
         ****************************************************************************
         */

        /**
         ****************************************************************************
         * STEP 15. destroy the master and child nodes
         ****************************************************************************
         */
        $deleteChildReview = $this->client->deleteChildProject( [
                'sessionId'  => $this->sessionId,
                'projectKey' => $childReview->dqfUUID,
                'projectId'  => $childReview->dqfId,
        ] );

        $this->assertEquals( 'OK', $deleteChildReview->status );

        $deleteChildProject = $this->client->deleteChildProject( [
                'sessionId'  => $this->sessionId,
                'projectKey' => $childTranslation->dqfUUID,
                'projectId'  => $childTranslation->dqfId,
        ] );

        $this->assertEquals( 'OK', $deleteChildProject->status );

        $deleteMasterProject = $this->client->deleteMasterProject( [
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProject->dqfUUID,
                'projectId'  => $masterProject->dqfId,
        ] );

        $this->assertEquals( 'OK', $deleteMasterProject->status );
    }

    /**
     * @return mixed
     */
    private function getSegmentOrigin($name)
    {
        $segmentOrigins = $this->client->getBasicAttributesAggregate([])['segmentOrigin'];

        foreach ($segmentOrigins as $segmentOrigin){
            if($segmentOrigin->name === $name){
                return $segmentOrigin->id;
            }
        }
    }
}
