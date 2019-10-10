<?php

namespace Matecat\Dqf\Tests\SessionProvider;

use Matecat\Dqf\Constants;
use Matecat\Dqf\Model\Entity\ChildProject;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Model\Entity\ReviewedSegment;
use Matecat\Dqf\Model\Entity\ReviewSettings;
use Matecat\Dqf\Model\Entity\SourceSegment;
use Matecat\Dqf\Model\Entity\TranslatedSegment;
use Matecat\Dqf\Model\ValueObject\ReviewBatch;
use Matecat\Dqf\Model\ValueObject\RevisionCorrection;
use Matecat\Dqf\Model\ValueObject\RevisionCorrectionItem;
use Matecat\Dqf\Model\ValueObject\RevisionError;
use Matecat\Dqf\Model\ValueObject\TranslationBatch;
use Matecat\Dqf\Repository\Api\ChildProjectRepository;
use Matecat\Dqf\Repository\Api\MasterProjectRepository;
use Matecat\Dqf\Repository\Api\ReviewRepository;
use Matecat\Dqf\Repository\Api\TranslationRepository;
use Matecat\Dqf\Tests\BaseTest;
use Ramsey\Uuid\Uuid;

class TranslationRepositoryTest extends BaseTest
{

    /**
     * @var MasterProjectRepository
     */
    private $masterProjectRepo;

    /**
     * @var ChildProjectRepository
     */
    private $childProjectRepo;

    /**
     * @var TranslationRepository
     */
    private $translationRepository;

    /**
     * @var ReviewRepository
     */
    private $reviewRepository;

    /**
     * @throws \Matecat\Dqf\Exceptions\SessionProviderException
     */
    protected function setUp()
    {
        parent::setUp();
        $this->masterProjectRepo     = new MasterProjectRepository($this->client, $this->sessionId);
        $this->childProjectRepo      = new ChildProjectRepository($this->client, $this->sessionId);
        $this->translationRepository = new TranslationRepository($this->client, $this->sessionId);
        $this->reviewRepository      = new ReviewRepository($this->client, $this->sessionId);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function save_a_translation_batch()
    {
        // create the master project
        $masterProject = new MasterProject('master-project-test', 'it-IT', 1, 2, 3, 1);

        // file(s)
        $file = new File('test-file', 3);
        $file->setClientId(Uuid::uuid4()->toString());
        $masterProject->addFile($file);

        // assoc targetLang to file(s)
        $masterProject->assocTargetLanguageToFile('en-US', $file);
        $masterProject->assocTargetLanguageToFile('fr-FR', $file);

        // review settings
        $reviewSettings = new ReviewSettings(Constants::PROJECT_TYPE_COMBINED);
        $reviewSettings->setErrorCategoryIds0(1);
        $reviewSettings->setErrorCategoryIds1(2);
        $reviewSettings->setErrorCategoryIds2(3);
        $reviewSettings->setSeverityWeights('[{"severityId":"1","weight":1}, {"severityId":"2","weight":2}, {"severityId":"3","weight":3}, {"severityId":"4","weight":4}]');
        $reviewSettings->setPassFailThreshold(0.00);
        $masterProject->setReviewSettings($reviewSettings);

        // source segments
        foreach ($this->getSourceSegments($file) as $sourceSegment) {
            $masterProject->addSourceSegment($sourceSegment);
        }

        // save the master project
        $this->masterProjectRepo->save($masterProject);

        // create the child project
        $childProject = new ChildProject(Constants::PROJECT_TYPE_TRANSLATION);
        $childProject->setParentProject($masterProject);
        $childProject->setName('Translation Job');

        // assoc targetLang to file(s)
        $childProject->assocTargetLanguageToFile('en-US', $masterProject->getFiles()[ 0 ]);
        $childProject->assocTargetLanguageToFile('fr-FR', $masterProject->getFiles()[ 0 ]);

        // review settings
        $reviewSettings = new ReviewSettings(Constants::PROJECT_TYPE_COMBINED);
        $reviewSettings->setErrorCategoryIds0(1);
        $reviewSettings->setErrorCategoryIds1(2);
        $reviewSettings->setErrorCategoryIds2(3);
        $reviewSettings->setSeverityWeights('[{"severityId":"1","weight":1}, {"severityId":"2","weight":2}, {"severityId":"3","weight":3}, {"severityId":"4","weight":4}]');
        $reviewSettings->setPassFailThreshold(0.00);
        $childProject->setReviewSettings($reviewSettings);

        // save the child project
        $this->childProjectRepo->save($childProject);

        // build the translation batch
        $translationBatch = new TranslationBatch($childProject, $file, 'en-US');
        $segmTrans1       = new TranslatedSegment($childProject, $file, 22, 1, 'en-US', $this->getSourceSegments($file)[ 0 ], '', 'The frog in Spain');
        $segmTrans2       = new TranslatedSegment($childProject, $file, 22, 2, 'en-US', $this->getSourceSegments($file)[ 1 ], 'croaks in countryside matus.', 'croaks in countryside.');
        $segmTrans3       = new TranslatedSegment($childProject, $file, 22, 3, 'en-US', $this->getSourceSegments($file)[ 2 ], 'This is just a tongue twister', '');

        $translationBatch->addSegment($segmTrans1);
        $translationBatch->addSegment($segmTrans2);
        $translationBatch->addSegment($segmTrans3);

        // save the translation batch
        $translationBatch = $this->translationRepository->save($translationBatch);

        $this->assertInstanceOf(TranslationBatch::class, $translationBatch);

        /** @var TranslationBatch $translationBatch */
        foreach ($translationBatch->getSegments() as $segment) {
            $this->assertNotNull($segment->getDqfId());
        }

        //$this->update_a_single_segment_translation($translationBatch->getSegments()[0]);
        $this->create_a_review_child_project_and_then_submit_a_revision($translationBatch->getSegments()[0], $file);
    }

    /**
     * @param TranslatedSegment $segment
     */
    public function update_a_single_segment_translation( TranslatedSegment $segment ) {
        $segment->setTargetSegment( 'The frog in Spain' );
        $segment->setEditedSegment( 'The frog in Spain (from Barcelona)' );

        $this->translationRepository->update( $segment );
    }

    /**
     * @param TranslatedSegment $segment
     * @param File              $file
     *
     * @throws \Exception
     */
    public function create_a_review_child_project_and_then_submit_a_revision(TranslatedSegment $segment, File $file)
    {
        $childProject = new ChildProject(Constants::PROJECT_TYPE_REVIEW);
        $childProject->setParentProject($segment->getChildProject());
        $childProject->setName('Review Job');

        // assoc targetLang to file(s)
        $childProject->assocTargetLanguageToFile('en-US', $file);
        $childProject->assocTargetLanguageToFile('fr-FR', $file);

        // review settings
        $reviewSettings = new ReviewSettings(Constants::PROJECT_TYPE_COMBINED);
        $reviewSettings->setErrorCategoryIds0(9);
        $reviewSettings->setErrorCategoryIds1(10);
        $reviewSettings->setErrorCategoryIds2(11);
        $reviewSettings->setSeverityWeights('[{"severityId":"1","weight":1}, {"severityId":"2","weight":2}, {"severityId":"3","weight":3}, {"severityId":"4","weight":4}]');
        $reviewSettings->setPassFailThreshold(0.00);
        $childProject->setReviewSettings($reviewSettings);

        // save the child project
        $childReview = $this->childProjectRepo->save($childProject);

        // create a segment review batch
        $correction = new RevisionCorrection('Another review comment', 10000);
        $correction->addItem(new RevisionCorrectionItem('review', 'deleted'));
        $correction->addItem(new RevisionCorrectionItem('Another comment', 'unchanged'));

        $reviewedSegment = new ReviewedSegment('this is a comment');
        $reviewedSegment->addError(new RevisionError(11, 2));
        $reviewedSegment->addError(new RevisionError(9, 1, 1, 5));
        $reviewedSegment->setCorrection($correction);

        $reviewedSegment2 = new ReviewedSegment('this is another comment');
        $reviewedSegment2->addError(new RevisionError(10, 2));
        $reviewedSegment2->addError(new RevisionError(11, 1, 1, 5));
        $reviewedSegment2->setCorrection($correction);

        $batchId = Uuid::uuid4()->toString();
        $reviewBatch = new ReviewBatch($childReview, $file, 'en-US', $segment, $batchId);
        $reviewBatch->addReviewedSegment($reviewedSegment);
        $reviewBatch->addReviewedSegment($reviewedSegment2);

        $batch = $this->reviewRepository->save($reviewBatch);

        $this->assertInstanceOf(ReviewBatch::class, $batch);

        foreach ($batch->getReviewedSegments() as $reviewedSegment){
            $this->assertNotNull($reviewedSegment->getClientId());
        }
    }

    /**
     * @param File $file
     *
     * @return array
     * @throws \Exception
     */
    private function getSourceSegments(File $file)
    {
        $segments = [];
        $faker    = \Faker\Factory::create();

        for ($i = 1; $i < 4; $i++) {
            $sourceSegment = new SourceSegment($file, $i, $faker->realText(100));
            $sourceSegment->setClientId(Uuid::uuid4()->toString());
            $segments[] = $sourceSegment;
        }

        return $segments;
    }
}
