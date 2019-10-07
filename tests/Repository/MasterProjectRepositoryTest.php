<?php

namespace Matecat\Dqf\Tests\SessionProvider;

use Faker\Factory;
use Matecat\Dqf\Client;
use Matecat\Dqf\Model\Entity\Language;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Repository\Api\MasterProjectRepository;
use Matecat\Dqf\Repository\Persistence\InMemoryDqfUserRepository;
use Matecat\Dqf\SessionProvider;
use Matecat\Dqf\Tests\BaseTest;

class MasterProjectRepositoryTest extends BaseTest
{
    /**
     * @var MasterProjectRepository
     */
    private $repo;

    protected function setUp() {
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
            $l = new Language('it-IT');

            $this->repo->save($l);
        } catch (\Exception $e){
            $this->assertEquals('Entity provided is not an instance of MasterProject', $e->getMessage());
        }

        $masterProject = new MasterProject('master-project-test', 'it-IT', 1, 2, 3, 1);
        $this->repo->save($masterProject);

        $this->get_a_master_project($masterProject->getDqfId(), $masterProject->getDqfUuid());
        $this->delete_a_master_project($masterProject->getDqfId(), $masterProject->getDqfUuid());
    }

    /**
     * Update a master project
     *
     * @param $dqfId
     * @param $dqfUuid
     */
    public function update_a_master_project($dqfId, $dqfUuid)
    {

    }

    /**
     * Get a master project
     *
     * @param $dqfId
     * @param $dqfUuid
     */
    public function get_a_master_project($dqfId, $dqfUuid)
    {
        $masterProject = $this->repo->get($dqfId, $dqfUuid);

        $this->assertEquals($masterProject->getDqfId(), $dqfId);
        $this->assertEquals($masterProject->getDqfUuid(), $dqfUuid);
    }

    /**
     * Delete a master project
     *
     * @param $dqfId
     * @param $dqfUuid
     */
    public function delete_a_master_project($dqfId, $dqfUuid)
    {
        $masterProject = $this->repo->delete($dqfId, $dqfUuid);

        $this->assertEquals(1, $masterProject);
    }
}