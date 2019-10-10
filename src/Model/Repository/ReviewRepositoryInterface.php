<?php

namespace Matecat\Dqf\Model\Repository;

use Matecat\Dqf\Model\ValueObject\ReviewBatch;

interface ReviewRepositoryInterface
{
    /**
     * @param ReviewBatch $batch
     *
     * @return mixed
     */
    public function save( ReviewBatch $batch );

    /**
     * @param ReviewBatch $batch
     *
     * @return mixed
     */
    public function update( ReviewBatch $batch );
}
