<?php

namespace Matecat\Dqf\Model\ValueObject;

use Matecat\Dqf\Model\Entity\ChildProject;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\Language;
use Matecat\Dqf\Model\Entity\ReviewedSegment;
use Matecat\Dqf\Model\Entity\TranslatedSegment;

class ReviewBatch
{
    /**
     * @var ChildProject
     */
    private $childProject;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Language
     */
    private $targetLanguage;

    /**
     * @var TranslatedSegment
     */
    private $translation;

    /**
     * @var bool
     */
    private $overwrite;

    /**
     * @var string
     */
    private $batchId;

    /**
     * @var ReviewedSegment
     */
    private $reviewedSegment;

    /**
     * ReviewBatch constructor.
     *
     * @param ChildProject      $childProject
     * @param File              $file
     * @param string            $targetLanguageCode
     * @param TranslatedSegment $translation
     * @param string            $batchId
     * @param bool              $overwrite
     */
    public function __construct(ChildProject $childProject, File $file, $targetLanguageCode, TranslatedSegment $translation, $batchId, $overwrite = true)
    {
        $this->childProject   = $childProject;
        $this->file           = $file;
        $this->targetLanguage = new Language($targetLanguageCode);
        $this->translation    = $translation;
        $this->batchId        = $batchId;
        $this->overwrite      = $overwrite;
    }

    /**
     * @return ChildProject
     */
    public function getChildProject()
    {
        return $this->childProject;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return Language
     */
    public function getTargetLanguage()
    {
        return $this->targetLanguage;
    }

    /**
     * @return TranslatedSegment
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * @return bool
     */
    public function isOverwrite()
    {
        return $this->overwrite;
    }

    /**
     * @return string
     */
    public function getBatchId()
    {
        return $this->batchId;
    }

    /**
     * @return ReviewedSegment
     */
    public function getReviewedSegment()
    {
        return $this->reviewedSegment;
    }

    /**
     * @param ReviewedSegment $reviewedSegment
     */
    public function setReviewedSegment(ReviewedSegment $reviewedSegment)
    {
        $this->reviewedSegment = $reviewedSegment;
    }
}
