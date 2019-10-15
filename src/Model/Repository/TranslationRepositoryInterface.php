<?php

namespace Matecat\Dqf\Model\Repository;

use Matecat\Dqf\Model\Entity\TranslatedSegment;
use Matecat\Dqf\Model\ValueObject\TranslationBatch;

interface TranslationRepositoryInterface
{

    /**
     * @param TranslationBatch $batch
     *
     * @return mixed
     */
    public function save(TranslationBatch $batch);

    /**
     * @param TranslatedSegment $translatedSegment
     *
     * @return bool
     */
    public function update(TranslatedSegment $translatedSegment);
}
