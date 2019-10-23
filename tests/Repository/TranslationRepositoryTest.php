<?php

namespace Matecat\Dqf\Tests\Repository;

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
        $masterProject = new MasterProject('master-workflow-test', 'it-IT', 1, 2, 3, 1);

        // file(s)
        $file = new File('original-filename', 3);
        $file->setClientId(Uuid::uuid4()->toString());
        $masterProject->addFile($file);

        // assoc targetLang to file(s)
        $masterProject->assocTargetLanguageToFile('en-US', $file);
        $masterProject->assocTargetLanguageToFile('fr-FR', $file);

        // review settings
        $reviewSettings = new ReviewSettings(Constants::REVIEW_TYPE_COMBINED);
        $reviewSettings->setErrorCategoryIds0(1);
        $reviewSettings->setErrorCategoryIds1(2);
        $reviewSettings->setErrorCategoryIds2(3);
        $reviewSettings->setSeverityWeights('[{"severityId":"1","weight":1}, {"severityId":"2","weight":2}, {"severityId":"3","weight":3}, {"severityId":"4","weight":4}]');
        $reviewSettings->setPassFailThreshold(0.00);
        $masterProject->setReviewSettings($reviewSettings);

        // source segments
        foreach ($this->getSourceSegmentsArray($file) as $sourceSegment) {
            $masterProject->addSourceSegment($sourceSegment);
        }

        // save the master project
        $this->masterProjectRepo->save($masterProject);

        // create the child project
        $childProject = new ChildProject(Constants::PROJECT_TYPE_TRANSLATION);
        $childProject->setParentProject($masterProject);
        $childProject->setName('child-workflow-test');
        $childProject->setIsDummy(true);

        // assoc targetLang to file(s)
        $childProject->assocTargetLanguageToFile('en-US', $masterProject->getFiles()[ 0 ]);
        $childProject->assocTargetLanguageToFile('fr-FR', $masterProject->getFiles()[ 0 ]);

        // review settings
        $reviewSettings = new ReviewSettings(Constants::REVIEW_TYPE_COMBINED);
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

        foreach ($this->getTargetSegmentsArray($childProject, $file) as $segmTrans) {
            $translationBatch->addSegment($segmTrans);
        }

        // save the translation batch
        $translationBatch = $this->translationRepository->save($translationBatch);

        $this->assertInstanceOf(TranslationBatch::class, $translationBatch);

        /** @var TranslationBatch $translationBatch */
        foreach ($translationBatch->getSegments() as $segment) {
            $this->assertNotNull($segment->getDqfId());
        }

        $firstSegment = $translationBatch->getSegments()[0];

        // update a single segment translation
        $this->update_a_single_segment_translation($firstSegment);

        // create a review project and then submit revision(s)
        $this->create_a_review_child_project_and_then_submit_a_revision($firstSegment, $file);

        // delete child project
        $deleteChildProject = $this->childProjectRepo->delete($childProject);
        $this->assertEquals(1, $deleteChildProject);

        // delete master project
        $deleteMasterProject = $this->masterProjectRepo->delete($masterProject);
        $this->assertEquals(1, $deleteMasterProject);
    }

    /**
     * @param TranslatedSegment $segment
     */
    public function update_a_single_segment_translation(TranslatedSegment $segment)
    {
        $segment->setTargetSegment('The frog in Spain');
        $segment->setEditedSegment('The frog in Spain (from Barcelona)');

        $update = $this->translationRepository->update($segment);

        $this->assertTrue($update);
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

        try {
            $this->childProjectRepo->save($childProject);
        } catch (\DomainException $e) {
            $this->assertEquals('A \'review\' ChildProject MUST have set review settings', $e->getMessage());
        }

        // review settings
        $reviewSettings = new ReviewSettings(Constants::REVIEW_TYPE_COMBINED);
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

        foreach ($batch->getReviewedSegments() as $reviewedSegment) {
            $this->assertNotNull($reviewedSegment->getClientId());
        }

        // resetting reviews before deleting all the project and child nodes
        $emptyReviewBatch = new ReviewBatch($childReview, $file, 'en-US', $segment, $batchId);
        $emptyBatch = $this->reviewRepository->save($emptyReviewBatch);

        $this->assertNull($emptyBatch->getReviewedSegments());

        // deleting the review project
        $delete = $this->childProjectRepo->delete($childReview);

        $this->assertEquals(1, $delete);
    }

    /**
     * @param File $file
     *
     * @return array
     * @throws \Exception
     */
    private function getSourceSegmentsArray(File $file)
    {
        $segments = [];

        foreach ($this->sourceFile[ 'segments' ] as $segment) {
            $sourceSegment = new SourceSegment($file, $segment['index'], $segment['sourceSegment']);
            $sourceSegment->setClientId($segment['clientId']);
            $segments[] = $sourceSegment;
        }

        return $segments;
    }

    /**
     * @param ChildProject $childProject
     * @param File         $file
     *
     * @return array
     * @throws \Exception
     */
    protected function getTargetSegmentsArray(ChildProject $childProject, File $file)
    {
        $translations = [];

        foreach ($this->targetFile['segmentPairs'] as $key => $segment) {
            $translations[] = new TranslatedSegment(
                $childProject,
                $file,
                $segment['mtEngineId'],
                $segment['segmentOriginId'],
                $this->targetFile['lang'],
                $this->getSourceSegmentsArray($file)[$key],
                $segment['targetSegment'],
                $segment['editedSegment']
            );
        }

        return $translations;
    }
}
