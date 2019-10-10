<?php

namespace Matecat\Dqf\Model\Repository;

use Matecat\Dqf\Model\Entity\TranslatedSegment;
use Matecat\Dqf\Model\ValueObject\TranslationBatch;

interface TranslationRepositoryInterface {
    /**
     * @param TranslationBatch $batch
     *
     * @return mixed
     */
    public function save( TranslationBatch $batch );

    /**
     * @param TranslatedSegment $segment
     *
     * @return mixed
     */
    public function update( TranslatedSegment $segment );
}
