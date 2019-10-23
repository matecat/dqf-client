<?php

namespace Matecat\Dqf\Tests\Repository;

use Matecat\Dqf\Exceptions\SessionProviderException;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Repository\Api\MasterProjectRepository;
use Matecat\Dqf\Tests\BaseTest;
use Ramsey\Uuid\Uuid;

class TmsWorkflowTest extends BaseTest
{
    /**
     * @var MasterProjectRepository
     */
    private $repo;

    /**
     * @throws SessionProviderException
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
    public function link_two_master_projects_and_cannot_delete_the_root()
    {
        /**
         ****************************************************************************
         * create a master project
         ****************************************************************************
         */

        $rootProject = new MasterProject('root-project-test', 'it-IT', 1, 2, 3, 1);

        $rootFile = new File('test-file', 3);
        $rootFile->setClientId(Uuid::uuid4()->toString());

        $rootProject->addFile($rootFile);
        $rootProject->assocTargetLanguageToFile('en-US', $rootFile);

        $this->repo->save($rootProject);

        /**
         ****************************************************************************
         * create a master project linked to the first one
         ****************************************************************************
         */

        $masterProject = new MasterProject('master-project-test', 'it-IT', 1, 2, 3, 1);
        $masterProject->setTmsProjectKey($rootProject->getDqfId());

        $masterFile = new File('test-file', 3);
        $masterFile->setClientId(Uuid::uuid4()->toString());
        $masterFile->setTmsFileId($rootFile->getDqfId());

        $masterProject->addFile($masterFile);
        $masterProject->setTmsProjectKey($rootProject->getDqfUuid());
        $masterProject->assocTargetLanguageToFile('en-US', $masterFile);

        $this->repo->save($masterProject);

        // cannot delete the root project
        try {
            $this->repo->delete($rootProject);
        } catch (\Exception $e){
            $error = json_decode($e->getMessage());

            $this->assertEquals("Error[Cannot delete or update a parent row: a foreign key constraint fails (`dqf_api`.`project`, CONSTRAINT `fk-project-project-tms` FOREIGN KEY (`tms_project_id`) REFERENCES `project` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION)]", $error->exceptionMessage);
            $this->assertEquals(6, $error->exceptionCode->code);
            $this->assertEquals(500, $error->exceptionCode->status);
        }

        // delete the master and then the root
        $this->repo->delete($masterProject);
        $this->repo->delete($rootProject);
    }
}