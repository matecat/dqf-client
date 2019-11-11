<?php

namespace Matecat\Dqf\Model\ValueObject;

use Matecat\Dqf\Model\Entity\BaseApiEntity;
use Matecat\Dqf\Model\Entity\ChildProject;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\Language;
use Matecat\Dqf\Model\Entity\TranslatedSegment;

class TranslationBatch extends BaseApiEntity
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
     * @var TranslatedSegment[]
     */
    private $segments;

    /**
     * Translation constructor.
     *
     * @param ChildProject $childProject
     * @param File         $file
     * @param string       $targetLanguageCode
     */
    public function __construct(ChildProject $childProject, File $file, $targetLanguageCode)
    {
        $this->childProject = $childProject;

        if (false === $this->childProject->getParentProject()->hasFile($file)) {
            throw new \DomainException('The file ' . $file->getName() . ' does not belong to master project');
        }

        if (false === $this->childProject->getParentProject()->hasTargetLanguage($targetLanguageCode)) {
            throw new \DomainException('The master project has not set ' . $targetLanguageCode . ' as a target language');
        }

        $this->file           = $file;
        $this->targetLanguage = new Language($targetLanguageCode);
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
     * @param TranslatedSegment $segment
     */
    public function addSegment(TranslatedSegment $segment)
    {
        $this->segments[] = $segment;
    }

    /**
     * @return TranslatedSegment[]
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @return int
     */
    public function getSegmentsCount()
    {
        return count($this->segments);
    }
}
