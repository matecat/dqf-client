<?php

namespace Matecat\Dqf\Tests\SessionProvider;

use Matecat\Dqf\Constants;
use Matecat\Dqf\Model\Entity\ChildProject;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\Language;
use Matecat\Dqf\Model\Entity\AbstractProject;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Model\Entity\ReviewSettings;
use Matecat\Dqf\Model\Entity\SourceSegment;
use Matecat\Dqf\Repository\Api\ChildProjectRepository;
use Matecat\Dqf\Repository\Api\MasterProjectRepository;
use Matecat\Dqf\Tests\BaseTest;
use Ramsey\Uuid\Uuid;

class ChildProjectRepositoryTest extends BaseTest
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
     * @throws \Matecat\Dqf\Exceptions\SessionProviderException
     */
    protected function setUp()
    {
        parent::setUp();

        $this->masterProjectRepo = new MasterProjectRepository($this->client, $this->sessionId);
        $this->childProjectRepo  = new ChildProjectRepository($this->client, $this->sessionId);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function save_a_child_project()
    {
        // create the master project
        $masterProject = new MasterProject('master-project-test', 'it-IT', 1, 2, 3, 1);

        // file(s)
        $file = new File('test-file', 200);
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

        // test all exceptions during creation of a child project
        try {
            $childProject = new ChildProject('dsadsadsa');
            $this->childProjectRepo->save($childProject);
        } catch (\DomainException $e) {
            $this->assertEquals('dsadsadsais not a valid type. [Allowed: translation,review]', $e->getMessage());
        }

        try {
            $childProject = new ChildProject(Constants::PROJECT_TYPE_TRANSLATION);
            $this->childProjectRepo->save($childProject);
        } catch (\DomainException $e) {
            $this->assertEquals('MasterProject MUST be set during creation of a ChildProject', $e->getMessage());
        }

        try {
            $language = new Language('it-IT');
            $this->childProjectRepo->save($language);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('Entity provided is not an instance of ChildProject', $e->getMessage());
        }

        // create the child project
        $childProject = new ChildProject(Constants::PROJECT_TYPE_TRANSLATION);
        $childProject->setParentProject($masterProject);
        $childProject->setName('Translation Job');
        $childProject->setAssigner('giuseppe@gmail.com');

        // assoc targetLang to file(s)
        $childProject->assocTargetLanguageToFile('en-US', $masterProject->getFiles()[0]);

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

        // get a child project
        $this->get_a_child_project($childProject->getDqfId(), $childProject->getDqfUuid());

        // update the child project
        $this->update_a_child_project($childProject->getDqfId(), $childProject->getDqfUuid(), $masterProject);

        // delete the child project
        $this->delete_a_child_project($childProject->getDqfId(), $childProject->getDqfUuid());

        // delete the master project
        $this->delete_a_master_project($masterProject->getDqfId(), $masterProject->getDqfUuid());
    }

    /**
     * Get a child project
     *
     * @param $dqfId
     * @param $dqfUuid
     */
    public function get_a_child_project($dqfId, $dqfUuid)
    {
        /** @var ChildProject $childProject */
        $childProject = $this->childProjectRepo->get($dqfId, $dqfUuid);

        $this->assertInstanceOf(ChildProject::class, $childProject);
        $this->assertEquals($childProject->getDqfId(), $dqfId);
        $this->assertEquals($childProject->getDqfUuid(), $dqfUuid);
        $this->assertEquals($childProject->getName(), 'Translation Job');
        $this->assertEquals(['en-US',], array_keys($childProject->getTargetLanguageAssoc()));
        $this->assertInstanceOf(ReviewSettings::class, $childProject->getReviewSettings());
        $this->assertNotNull($childProject->getReviewSettings()->getDqfId());
        $this->assertNotNull($childProject->getReviewSettingsId());
        $this->assertEquals($childProject->getReviewSettings()->getDqfId(), $childProject->getReviewSettingsId());
    }

    /**
     * Update a child project
     *
     * @param               $dqfId
     * @param               $dqfUuid
     * @param AbstractProject $masterProject
     *
     * @throws \Exception
     */
    public function update_a_child_project($dqfId, $dqfUuid, AbstractProject $masterProject)
    {
        try {
            $childProject = new ChildProject(Constants::PROJECT_TYPE_TRANSLATION);
            $this->childProjectRepo->update($childProject);
        } catch (\DomainException $e) {
            $this->assertEquals('MasterProject MUST be set during the update of a ChildProject', $e->getMessage());
        }

        /** @var ChildProject $childProject */
        $childProject = $this->childProjectRepo->get($dqfId, $dqfUuid);
        $childProject->setName('Modified child name');
        $childProject->setParentProject($masterProject);

        // assoc targetLang to file(s)
        $childProject->assocTargetLanguageToFile('fr-FR', $masterProject->getFiles()[0]);

        // update the project
        $this->childProjectRepo->update($childProject);

        /** @var ChildProject $modifiedChildProject */
        $modifiedChildProject = $this->childProjectRepo->get($dqfId, $dqfUuid);

        $this->assertEquals($modifiedChildProject->getName(), 'Modified child name');
        $this->assertEquals(['en-US','fr-FR'], array_keys($modifiedChildProject->getTargetLanguageAssoc()));
    }

    /**
     * Delete a child project
     *
     * @param $dqfId
     * @param $dqfUuid
     */
    public function delete_a_child_project($dqfId, $dqfUuid)
    {
        $masterProject = $this->childProjectRepo->delete($dqfId, $dqfUuid);

        $this->assertEquals(1, $masterProject);
    }

    /**
     * Delete a master project
     *
     * @param $dqfId
     * @param $dqfUuid
     */
    public function delete_a_master_project($dqfId, $dqfUuid)
    {
        $masterProject = $this->masterProjectRepo->delete($dqfId, $dqfUuid);

        $this->assertEquals(1, $masterProject);
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

        for ($i = 1; $i < 201; $i++) {
            $sourceSegment = new SourceSegment($file, $i, $faker->realText(100));
            $sourceSegment->setClientId(Uuid::uuid4()->toString());
            $segments[] = $sourceSegment;
        }

        return $segments;
    }
}
