<?php

namespace Matecat\Dqf\Tests\SessionProvider;

use Matecat\Dqf\Model\Entity\ChildProject;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Model\Entity\ReviewSettings;
use Matecat\Dqf\Model\Entity\SourceSegment;
use Matecat\Dqf\Model\Entity\TranslatedSegment;
use Matecat\Dqf\Model\Entity\TranslationBatch;
use Matecat\Dqf\Repository\Api\ChildProjectRepository;
use Matecat\Dqf\Repository\Api\MasterProjectRepository;
use Matecat\Dqf\Repository\Api\TranslationBatchRepository;
use Matecat\Dqf\Tests\BaseTest;
use Ramsey\Uuid\Uuid;

class TranslationBatchRepositoryTest extends BaseTest {

    /**
     * @var MasterProjectRepository
     */
    private $masterProjectRepo;

    /**
     * @var ChildProjectRepository
     */
    private $childProjectRepo;

    /**
     * @var TranslationBatchRepository
     */
    private $translationBatchRepository;

    /**
     * @throws \Matecat\Dqf\Exceptions\SessionProviderException
     */
    protected function setUp() {
        parent::setUp();
        $this->masterProjectRepo = new MasterProjectRepository( $this->client, $this->sessionId );
        $this->childProjectRepo  = new ChildProjectRepository( $this->client, $this->sessionId );
        $this->translationBatchRepository = new TranslationBatchRepository( $this->client, $this->sessionId );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function save_a_translation_batch() {
        // create the master project
        $masterProject = new MasterProject( 'master-project-test', 'it-IT', 1, 2, 3, 1 );

        // file(s)
        $file = new File( 'test-file', 3);
        $file->setClientId( Uuid::uuid4()->toString() );
        $masterProject->addFile( $file );

        // assoc targetLang to file(s)
        $masterProject->assocTargetLanguageToFile( 'en-US', $file );
        $masterProject->assocTargetLanguageToFile( 'fr-FR', $file );

        // review settings
        $reviewSettings = new ReviewSettings( 'combined' );
        $reviewSettings->setErrorCategoryIds0( 1 );
        $reviewSettings->setErrorCategoryIds1( 2 );
        $reviewSettings->setErrorCategoryIds2( 3 );
        $reviewSettings->setSeverityWeights( '[{"severityId":"1","weight":1}, {"severityId":"2","weight":2}, {"severityId":"3","weight":3}, {"severityId":"4","weight":4}]' );
        $reviewSettings->setPassFailThreshold( 0.00 );
        $masterProject->setReviewSettings( $reviewSettings );

        // source segments
        foreach ( $this->getSourceSegments( $file ) as $sourceSegment ) {
            $masterProject->addSourceSegment( $sourceSegment );
        }

        // save the master project
        $this->masterProjectRepo->save( $masterProject );

        // create the child project
        $childProject = new ChildProject( 'translation' );
        $childProject->setMasterProject( $masterProject );
        $childProject->setName( 'Translation Job' );
        $childProject->setAssigner( 'giuseppe@gmail.com' );

        // assoc targetLang to file(s)
        $childProject->assocTargetLanguageToFile( 'en-US', $masterProject->getFiles()[0] );
        $childProject->assocTargetLanguageToFile( 'fr-FR', $masterProject->getFiles()[0] );

        // save the child project
        $this->childProjectRepo->save( $childProject );

        $aa = $this->client->getSourceSegmentIdsForAFile([
                'sessionId' => $this->sessionId,
                'projectKey' => $childProject->getDqfUuid(),
                'projectId' => $childProject->getDqfId(),
                'fileId' => $file->getDqfId(),
                'targetLangCode' => 'fr-FR',
        ]);

        var_dump($aa);

        die();


        // build the translation batch
        $translationBatch = new TranslationBatch($childProject, $file, 'en-US');
        $segmTrans1 = new TranslatedSegment($childProject, $file, 'en-US', $this->getSourceSegments($file)[0], 'blah', 'blah blah blah');
        $segmTrans2 = new TranslatedSegment($childProject, $file, 'en-US', $this->getSourceSegments($file)[1], 'blah', 'blah blah blah');
        $segmTrans3 = new TranslatedSegment($childProject, $file, 'en-US', $this->getSourceSegments($file)[2], 'blah', 'blah blah blah');

        $translationBatch->addSegment($segmTrans1);
        $translationBatch->addSegment($segmTrans2);
        $translationBatch->addSegment($segmTrans3);

        // save the translation batch
        $this->translationBatchRepository->save( $translationBatch );
    }



    /**
     * @param File $file
     *
     * @return array
     * @throws \Exception
     */
    private function getSourceSegments( File $file ) {
        $segments = [];
        $faker    = \Faker\Factory::create();

        for ( $i = 1; $i < 4; $i++ ) {
            $sourceSegment = new SourceSegment( $file, $i, $faker->realText( 100 ) );
            $sourceSegment->setClientId( Uuid::uuid4()->toString() );
            $segments[] = $sourceSegment;
        }

        return $segments;
    }
}
