<?php

namespace Matecat\Dqf\Tests\Repository;

use Matecat\Dqf\Constants;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\Language;
use Matecat\Dqf\Model\Entity\AbstractProject;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Model\Entity\ReviewSettings;
use Matecat\Dqf\Model\Entity\SourceSegment;
use Matecat\Dqf\Repository\Api\MasterProjectRepository;
use Matecat\Dqf\Tests\BaseTest;
use Ramsey\Uuid\Uuid;

class MasterProjectRepositoryTest extends BaseTest
{
    /**
     * @var MasterProjectRepository
     */
    private $repo;

    /**
     * @throws \Matecat\Dqf\Exceptions\SessionProviderException
     */
    protected function setUp()
    {
        parent::setUp();

        $this->repo = new MasterProjectRepository($this->client, $this->sessionId);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function save_a_master_project()
    {
        try {
            $language = new Language('it-IT');

            $this->repo->save($language);
        } catch (\Exception $e) {
            $this->assertEquals('Entity provided is not an instance of MasterProject', $e->getMessage());
        }

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
        $reviewSettings = new ReviewSettings(Constants::REVIEW_TYPE_COMBINED);
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

        // save project
        $this->repo->save($masterProject);

        // get a master project
        $this->get_a_master_project($masterProject->getDqfId(), $masterProject->getDqfUuid());

        // update the master project
        $this->update_a_master_project($masterProject->getDqfId(), $masterProject->getDqfUuid());

        // delete the master project
        $this->delete_a_master_project($masterProject);
    }

    /**
     * Get a master project
     *
     * @param $dqfId
     * @param $dqfUuid
     */
    public function get_a_master_project($dqfId, $dqfUuid)
    {
        /** @var AbstractProject $masterProject */
        $masterProject = $this->repo->get($dqfId, $dqfUuid);

        $this->assertEquals($masterProject->getDqfId(), $dqfId);
        $this->assertEquals($masterProject->getDqfUuid(), $dqfUuid);
        $this->assertInstanceOf(ReviewSettings::class, $masterProject->getReviewSettings());
        $this->assertNotNull($masterProject->getReviewSettings()->getDqfId());
        $this->assertNotNull($masterProject->getSourceLanguage()->getDqfId());
    }

    /**
     * Update a master project
     *
     * @param $dqfId
     * @param $dqfUuid
     */
    public function update_a_master_project($dqfId, $dqfUuid)
    {
        /** @var AbstractProject $masterProject */
        $masterProject = $this->repo->get($dqfId, $dqfUuid);
        $masterProject->setName('Modified name');

        $masterProject->getFiles()[0]->setName('test-file-changed');
        $masterProject->modifyTargetLanguageToFile('en-US', 'pt-PT', $masterProject->getFiles()[0]);
        $masterProject->assocTargetLanguageToFile('es-ES', $masterProject->getFiles()[0]);

        $this->repo->update($masterProject);

        /** @var AbstractProject $modifiedMasterProject */
        $modifiedMasterProject = $this->repo->get($dqfId, $dqfUuid);

        $this->assertEquals($modifiedMasterProject->getName(), 'Modified name');
        $this->assertEquals($modifiedMasterProject->getFiles()[0]->getName(), 'test-file-changed');
        $this->assertEquals(['fr-FR','pt-PT','es-ES',], array_keys($modifiedMasterProject->getTargetLanguageAssoc()));
    }

    /**
     * Delete a master project
     *
     * @param MasterProject $masterProject
     */
    public function delete_a_master_project(MasterProject $masterProject)
    {
        $deletedProject = $this->repo->delete($masterProject);

        $this->assertEquals(1, $deletedProject);
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
        $faker = \Faker\Factory::create();

        for ($i=1; $i < 201; $i++) {
            $sourceSegment = new SourceSegment($file, $i, $faker->realText(100));
            $sourceSegment->setClientId(Uuid::uuid4()->toString());
            $segments[] = $sourceSegment;
        }

        return $segments;
    }
}
