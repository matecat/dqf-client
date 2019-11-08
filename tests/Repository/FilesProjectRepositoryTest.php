<?php

namespace Matecat\Dqf\Tests\Repository;

use Matecat\Dqf\Constants;
use Matecat\Dqf\Model\Entity\ChildProject;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\Language;
use Matecat\Dqf\Model\Entity\AbstractProject;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Model\Entity\ReviewSettings;
use Matecat\Dqf\Model\Entity\SourceSegment;
use Matecat\Dqf\Model\ValueObject\Severity;
use Matecat\Dqf\Repository\Api\ChildProjectRepository;
use Matecat\Dqf\Repository\Api\FilesRepository;
use Matecat\Dqf\Repository\Api\MasterProjectRepository;
use Matecat\Dqf\Tests\BaseTest;
use Ramsey\Uuid\Uuid;

class FilesProjectRepositoryTest extends BaseTest
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
     * @var FilesRepository
     */
    private $filesRepo;

    /**
     * @throws \Matecat\Dqf\Exceptions\SessionProviderException
     */
    protected function setUp()
    {
        parent::setUp();

        $this->masterProjectRepo = new MasterProjectRepository($this->client, $this->sessionId);
        $this->childProjectRepo  = new ChildProjectRepository($this->client, $this->sessionId);
        $this->filesRepo  = new FilesRepository($this->client, $this->sessionId);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function get_files_for_a_master_and_for_a_child_project()
    {
        // create the master project
        $masterProject = new MasterProject('master-project-test', 'it-IT', 1, 2, 3, 1);

        // file(s)
        $file = new File('test-file', 200);
        $file->setClientId(Uuid::uuid4()->toString());
        $file2 = new File('test-file-2', 200);
        $file2->setClientId(Uuid::uuid4()->toString());
        $file3 = new File('test-file-3', 200);
        $file3->setClientId(Uuid::uuid4()->toString());

        $masterProject->addFile($file);
        $masterProject->addFile($file2);
        $masterProject->addFile($file3);

        // assoc targetLang to file(s)
        $masterProject->assocTargetLanguageToFile('en-US', $file);
        $masterProject->assocTargetLanguageToFile('fr-FR', $file);
        $masterProject->assocTargetLanguageToFile('fr-FR', $file2);
        $masterProject->assocTargetLanguageToFile('fr-FR', $file3);

        // review settings
        $reviewSettings = new ReviewSettings(Constants::REVIEW_TYPE_COMBINED);
        $reviewSettings->addErrorCategoryId(1);
        $reviewSettings->addErrorCategoryId(2);
        $reviewSettings->addErrorCategoryId(3);
        $reviewSettings->addErrorCategoryId(4);
        $reviewSettings->addErrorCategoryId(5);

        $sev1 = new Severity(1, 1);
        $sev2 = new Severity(2, 2);
        $sev3 = new Severity(3, 3);
        $sev4 = new Severity(4, 4);

        $reviewSettings->addSeverityWeight($sev1);
        $reviewSettings->addSeverityWeight($sev2);
        $reviewSettings->addSeverityWeight($sev3);
        $reviewSettings->addSeverityWeight($sev4);

        $reviewSettings->setPassFailThreshold(0.00);
        $masterProject->setReviewSettings($reviewSettings);

        // source segments
        foreach ($this->getSourceSegments($file) as $sourceSegment) {
            $masterProject->addSourceSegment($sourceSegment);
        }

        // save the master project
        $this->masterProjectRepo->save($masterProject);

        // get file(s) for the master project
        $masterProjectFiles = $this->filesRepo->getByMasterProject($masterProject->getDqfId(), $masterProject->getDqfUuid());

        $this->assertCount(3, $masterProjectFiles);
        $this->assertEquals($masterProjectFiles[0]->getName(), 'test-file');
        $this->assertEquals($masterProjectFiles[1]->getName(), 'test-file-2');
        $this->assertEquals($masterProjectFiles[2]->getName(), 'test-file-3');

        // get a single file
        $childMasterFile = $this->filesRepo->getByIdAndMasterProject($masterProject->getDqfId(), $masterProject->getDqfUuid(), $masterProjectFiles[1]->getDqfId());

        $this->assertEquals($childMasterFile->getName(), 'test-file-2');

        // create the child project
        $childProject = new ChildProject(Constants::PROJECT_TYPE_TRANSLATION);
        $childProject->setParentProject($masterProject);
        $childProject->setName('Translation Job');
        $childProject->setAssigner('giuseppe@gmail.com');

        // assoc targetLang to file(s)
        $childProject->assocTargetLanguageToFile('en-US', $masterProject->getFiles()[0]);
        $childProject->assocTargetLanguageToFile('fr-FR', $masterProject->getFiles()[1]);
        $childProject->assocTargetLanguageToFile('fr-FR', $masterProject->getFiles()[2]);

        // save the child project
        $this->childProjectRepo->save($childProject);

        // get file(s) for the child project
        $childProjectFiles = $this->filesRepo->getByChildProject($childProject->getDqfId(), $childProject->getDqfUuid());

        $this->assertCount(3, $childProjectFiles);
        $this->assertEquals($childProjectFiles[0]->getName(), 'test-file');
        $this->assertEquals($childProjectFiles[1]->getName(), 'test-file-2');
        $this->assertEquals($childProjectFiles[2]->getName(), 'test-file-3');

        // get a single file
        $childProjectFile = $this->filesRepo->getByIdAndChildProject($childProject->getDqfId(), $childProject->getDqfUuid(), $childProjectFiles[2]->getDqfId());

        $this->assertEquals($childProjectFile->getName(), 'test-file-3');

        // delete the child project
        $this->delete_a_child_project($childProject);

        // delete the master project
        $this->delete_a_master_project($masterProject);
    }

    /**
     * Delete a child project
     *
     * @param ChildProject $childProject
     */
    public function delete_a_child_project(ChildProject $childProject)
    {
        $deletedProject = $this->childProjectRepo->delete($childProject);

        $this->assertEquals(1, $deletedProject);
    }

    /**
     * Delete a master project
     *
     * @param MasterProject $masterProject
     */
    public function delete_a_master_project(MasterProject $masterProject)
    {
        $deletedProject = $this->masterProjectRepo->delete($masterProject);

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
        $faker    = \Faker\Factory::create();

        for ($i = 1; $i < 201; $i++) {
            $sourceSegment = new SourceSegment($file, $i, $faker->realText(100));
            $sourceSegment->setClientId(Uuid::uuid4()->toString());
            $segments[] = $sourceSegment;
        }

        return $segments;
    }
}
