<?php

namespace Matecat\Dqf\Repository\Api;

use Matecat\Dqf\Model\Entity\BaseApiEntity;
use Matecat\Dqf\Model\Entity\ChildProject;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\FileTargetLang;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Model\Repository\ReviewRepositoryInterface;
use Matecat\Dqf\Model\ValueObject\ReviewBatch;

class ReviewBatchRepository extends AbstractApiRepository implements ReviewRepositoryInterface
{
    /**
     * @param ReviewBatch $batch
     *
     * @return mixed
     */
    public function save( ReviewBatch $batch ) {
        // TODO: Implement save() method.
    }

    /**
     * @param ReviewBatch $batch
     *
     * @return mixed
     */
    public function update( ReviewBatch $batch ) {
        // TODO: Implement update() method.
    }
}
