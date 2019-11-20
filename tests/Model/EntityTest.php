<?php

namespace Matecat\Dqf\Tests\Model;

use Matecat\Dqf\Model\Entity\ChildProject;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\Language;
use Matecat\Dqf\Model\Entity\AbstractProject;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Model\Entity\ReviewedSegment;
use Matecat\Dqf\Model\Entity\ReviewSettings;
use Matecat\Dqf\Model\Entity\SourceSegment;
use Matecat\Dqf\Model\Entity\TranslatedSegment;
use Matecat\Dqf\Model\ValueObject\ReviewBatch;
use Matecat\Dqf\Model\ValueObject\RevisionCorrection;
use Matecat\Dqf\Model\ValueObject\RevisionCorrectionItem;
use Matecat\Dqf\Model\ValueObject\RevisionError;
use Matecat\Dqf\Model\ValueObject\Severity;
use Matecat\Dqf\Model\ValueObject\TranslationBatch;
use Matecat\Dqf\Tests\BaseTest;
use Ramsey\Uuid\Uuid;

class EntityTest extends BaseTest
{
    /**
     * @test
     * @throws \Exception
     */
    public function create_the_master_project_model()
    {
        /**
         ****************************************************************************
         * Create a master project model
         ****************************************************************************
         */

        $clientId = Uuid::uuid4()->toString();

        try {
            new MasterProject('test-project', 'it-IT', 43242, 2, 1, 1);
        } catch (\DomainException $e) {
            $this->assertEquals($e->getMessage(), '43242 is not a valid value. [Allowed: 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]');
        }

        try {
            new MasterProject('test-project', 'it-IT', 1, 432432432, 3, 1);
        } catch (\DomainException $e) {
            $this->assertEquals($e->getMessage(), '432432432 is not a valid value. [Allowed: 1,2,3,4,16,5,6,7,8,9,10,11,12,13,14,15,17,18,19,20,21,22,23,24]');
        }

        try {
            new MasterProject('test-project', 'it-IT', 1, 2, 43243, 1);
        } catch (\DomainException $e) {
            $this->assertEquals($e->getMessage(), '43243 is not a valid value. [Allowed: 5,2,1,3,4]');
        }

        try {
            new MasterProject('test-project', 'it-IT', 1, 2, 3, 4);
        } catch (\DomainException $e) {
            $this->assertEquals($e->getMessage(), '4 is not a valid value. [Allowed: 1,2]');
        }

        $masterProject = new MasterProject('test-project', 'it-IT', 1, 2, 3, 1);
        $masterProject->setClientId($clientId);

        // add review settings
        $reviewSettings = new ReviewSettings('combined');
        $reviewSettings->addErrorCategoryId(1);
        $reviewSettings->addErrorCategoryId(2);
        $reviewSettings->addErrorCategoryId(3);
        $reviewSettings->addSeverityWeight(new Severity(1, 1));
        $reviewSettings->addSeverityWeight(new Severity(2, 2));
        $reviewSettings->setPassFailThreshold(0.00);
        $masterProject->setReviewSettings($reviewSettings);

        // add file(s)
        $file = new File('test-file', 3);
        $file->setClientId(Uuid::uuid4()->toString());

        $masterProject->addFile($file);

        // assoc target language to files
        $masterProject->assocTargetLanguageToFile('en-US', $file);
        $masterProject->assocTargetLanguageToFile('fr-FR', $file);

        // add source segments
        foreach ($this->getSourceSegments($file) as $sourceSegment) {
            $masterProject->addSourceSegment($sourceSegment);
        }

        // make assertions
        $this->assertInstanceOf(AbstractProject::class, $masterProject);
        $this->assertEquals(3, $masterProject->getSourceSegmentsCount($file->getName()));
        $this->assertCount(2, $masterProject->getTargetLanguages());
        $this->assertEquals($masterProject->getTargetLanguages()[ 0 ]->getLocaleCode(), 'en-US');
        $this->assertEquals($masterProject->getTargetLanguages()[ 1 ]->getLocaleCode(), 'fr-FR');
        $this->assertTrue($masterProject->hasTargetLanguage('fr-FR'));
        $this->assertFalse($masterProject->hasTargetLanguage('es-ES'));
        $this->assertEquals('[{"severityId":1,"weight":1},{"severityId":2,"weight":2}]', $masterProject->getReviewSettings()->getSeverityWeights());
        $this->assertEquals([1,2,3], $masterProject->getReviewSettings()->getErrorCategoryIds());

        try {
            $file3 = new File('test-file333', 3);
            $masterProject->assocTargetLanguageToFile('es-ES', $file3);
        } catch (\DomainException $e) {
            $this->assertEquals($e->getMessage(), 'test-file333 does not belong to the project');
        }

        try {
            new Language('dsadsadsadsa');
        } catch (\DomainException $e) {
            $this->assertEquals($e->getMessage(), 'dsadsadsadsa is not a valid locale code');
        }

        /**
         ****************************************************************************
         * Create a child 'translation' project model
         ****************************************************************************
         */

        $clientId = Uuid::uuid4()->toString();

        try {
            $childTranslation = new ChildProject('dsadsadsadsa');
            $childTranslation->setParentProject($masterProject);
            $childTranslation->setName('translation-test');
            $childTranslation->setClientId($clientId);
            $childTranslation->setIsDummy(true);
        } catch (\DomainException $e) {
            $this->assertEquals($e->getMessage(), 'dsadsadsadsais not a valid type. [Allowed: translation,review]');
        }

        $childTranslation = new ChildProject('translation');
        $childTranslation->setParentProject($masterProject);
        $childTranslation->setName('translation-test');
        $childTranslation->setClientId($clientId);
        $childTranslation->setIsDummy(true);

        // assoc target language to files
        $childTranslation->assocTargetLanguageToFile('en-US', $file);
        $childTranslation->assocTargetLanguageToFile('fr-FR', $file);

        // create a segment translation batch
        $translationBatch = new TranslationBatch($childTranslation, $file, 'en-US');
        $segmTrans1       = new TranslatedSegment(22, 5, 'en-US', $this->getSourceSegments($file)[ 0 ], 'blah', 'blah blah blah');
        $segmTrans2       = new TranslatedSegment(22, 5, 'en-US', $this->getSourceSegments($file)[ 1 ], 'blah', 'blah blah blah');
        $segmTrans3       = new TranslatedSegment(22, 5, 'en-US', $this->getSourceSegments($file)[ 2 ], 'blah', 'blah blah blah');

        try {
            new TranslatedSegment(22, 4343, 'en-US', $this->getSourceSegments($file)[ 0 ], 'blah', 'blah blah blah');
        } catch (\DomainException $e) {
            $this->assertEquals('4343 is not an allowed value. [Allowed: 1,2,3,4,5]', $e->getMessage());
        }

        $translationBatch->addSegment($segmTrans1);
        $translationBatch->addSegment($segmTrans2);
        $translationBatch->addSegment($segmTrans3);

        // make assertions
        $this->assertInstanceOf(ChildProject::class, $childTranslation);
        $this->assertEquals($childTranslation->getTargetLanguages()[ 0 ]->getLocaleCode(), 'en-US');
        $this->assertEquals($childTranslation->getTargetLanguages()[ 1 ]->getLocaleCode(), 'fr-FR');

        try {
            $file3 = new File('test-file333', 3);
            $childTranslation->assocTargetLanguageToFile('es-ES', $file3);
        } catch (\DomainException $e) {
            $this->assertEquals($e->getMessage(), 'test-file333 does not belong to the master project');
        }

        $this->assertEquals(3, $translationBatch->getSegmentsCount());

        /**
         ****************************************************************************
         * Create a child 'review' project model
         ****************************************************************************
         */

        $clientId = Uuid::uuid4()->toString();

        try {
            $childReview = new ChildProject('review');
            $childReview->setParentProject($masterProject);
            $childReview->setName('review-test');
            $childReview->setClientId($clientId);
            $childReview->setIsDummy(true);
        } catch (\DomainException $e) {
            $this->assertEquals($e->getMessage(), '\'isDummy\' MUST be set to false if project type is \'review\'');
        }

        $childReview = new ChildProject('review');
        $childReview->setParentProject($masterProject);
        $childReview->setName('review-test');
        $childReview->setClientId($clientId);
        $childReview->setIsDummy(false);

        // create a segment review batch
        $correction = new RevisionCorrection('Another review comment', 10000);
        $correction->addItem(new RevisionCorrectionItem('review', 'deleted'));
        $correction->addItem(new RevisionCorrectionItem('Another comment', 'unchanged'));

        try {
            $correction->addItem(new RevisionCorrectionItem('Another comment', 'fdsfdsfdsfsd'));
        } catch (\DomainException $e) {
            $this->assertEquals($e->getMessage(), 'fdsfdsfdsfsdis not a valid type. [Allowed: added,deleted,unchanged]');
        }

        $reviewedSegment = new ReviewedSegment('this is a comment');
        $reviewedSegment->addError(new RevisionError(11, 2));
        $reviewedSegment->addError(new RevisionError(9, 1, 1, 5));

        try {
            $reviewedSegment->addError(new RevisionError(9, 1, 11111, 5));
        } catch (\DomainException $e) {
            $this->assertEquals($e->getMessage(), '\'charPosStart\' cannot be greater than \'charPosEnd\'');
        }

        $reviewedSegment->setCorrection($correction);

        $batchId = Uuid::uuid4()->toString();

        $reviewBatch = new ReviewBatch($childReview, $file, 'en-US', $segmTrans1, $batchId);
        $reviewBatch->addReviewedSegment($reviewedSegment);

        $this->assertSame($reviewedSegment, $reviewBatch->getReviewedSegments()[0]);
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getSourceSegments(File $file)
    {
        $segments = [];

        $sources = [
                'La rana in Spagna',
                'gracida in campagna.',
                'Un semplice scioglilingua!',
        ];

        $i = 1;
        foreach ($sources as $source) {
            $sourceSegment = new SourceSegment($file, $i, $source);
            $sourceSegment->setClientId(Uuid::uuid4()->toString());
            $segments[] = $sourceSegment;

            $i++;
        }

        return $segments;
    }
}
