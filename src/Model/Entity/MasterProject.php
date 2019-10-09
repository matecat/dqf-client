<?php

namespace Matecat\Dqf\Model\Entity;

class MasterProject extends BaseApiEntity implements ProjectInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Language
     */
    private $sourceLanguage;

    /**
     * @var int
     */
    private $contentTypeId;

    /**
     * @var int
     */
    private $industryId;

    /**
     * @var int
     */
    private $processId;

    /**
     * @var int
     */
    private $qualityLevelId;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var string
     */
    private $tmsProjectKey;

    /**
     * @var ReviewSettings
     */
    private $reviewSettings;

    /**
     * @var File[]
     */
    private $files;

    /**
     * @var array
     */
    private $sourceSegments;

    /**
     * @var array
     */
    private $targetLanguageAssoc;

    /**
     * MasterProject constructor.
     *
     * @param $name
     * @param $sourceLanguageCode
     * @param $contentTypeId
     * @param $industryId
     * @param $processId
     * @param $qualityLevelId
     */
    public function __construct($name, $sourceLanguageCode, $contentTypeId, $industryId, $processId, $qualityLevelId)
    {
        $this->name = $name;
        $this->sourceLanguage = new Language($sourceLanguageCode);
        $this->contentTypeId = $contentTypeId;
        $this->industryId = $industryId;
        $this->processId = $processId;
        $this->qualityLevelId = $qualityLevelId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Language
     */
    public function getSourceLanguage()
    {
        return $this->sourceLanguage;
    }

    /**
     * @param Language $sourceLanguage
     */
    public function setSourceLanguage($sourceLanguage)
    {
        $this->sourceLanguage = $sourceLanguage;
    }

    /**
     * @return int
     */
    public function getContentTypeId()
    {
        return $this->contentTypeId;
    }

    /**
     * @param int $contentTypeId
     */
    public function setContentTypeId($contentTypeId)
    {
        $this->contentTypeId = $contentTypeId;
    }

    /**
     * @return int
     */
    public function getIndustryId()
    {
        return $this->industryId;
    }

    /**
     * @param int $industryId
     */
    public function setIndustryId($industryId)
    {
        $this->industryId = $industryId;
    }

    /**
     * @return int
     */
    public function getProcessId()
    {
        return $this->processId;
    }

    /**
     * @param int $processId
     */
    public function setProcessId($processId)
    {
        $this->processId = $processId;
    }

    /**
     * @return int
     */
    public function getQualityLevelId()
    {
        return $this->qualityLevelId;
    }

    /**
     * @param int $qualityLevelId
     */
    public function setQualityLevelId($qualityLevelId)
    {
        $this->qualityLevelId = $qualityLevelId;
    }

    /**
     * @return ReviewSettings
     */
    public function getReviewSettings()
    {
        return $this->reviewSettings;
    }

    /**
     * @param ReviewSettings $reviewSettings
     */
    public function setReviewSettings($reviewSettings)
    {
        $this->reviewSettings = $reviewSettings;
    }

    /**
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * @param string $templateName
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
    }

    /**
     * @return string
     */
    public function getTmsProjectKey()
    {
        return $this->tmsProjectKey;
    }

    /**
     * @param string $tmsProjectKey
     */
    public function setTmsProjectKey($tmsProjectKey)
    {
        $this->tmsProjectKey = $tmsProjectKey;
    }

    /**
     * @param $name
     *
     * @return File
     */
    public function getFile($name)
    {
        foreach ($this->getFiles() as $f) {
            if ($name === $f->getName()) {
                return $f;
            }
        }
    }

    /**
     * @return File[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param File $file
     *
     * @return bool
     */
    public function hasFile(File $file)
    {
        if (empty($this->getFiles())) {
            return false;
        }

        foreach ($this->getFiles() as $f) {
            if ($file->getName() === $f->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param File
     */
    public function addFile(File $file)
    {
        if (false === $this->hasFile($file)) {
            $this->files[] = $file;
        }
    }

    /**
     * @param      $languageCode
     * @param File $file
     * @param null $dqfId
     */
    public function assocTargetLanguageToFile($languageCode, File $file, $dqfId = null)
    {
        if (false === $this->hasFile($file)) {
            throw new \DomainException($file->getName() . ' does not belong to the project');
        }

        $fileTargetLang = new FileTargetLang($languageCode, $file);
        if ($dqfId) {
            $fileTargetLang->setDqfId($dqfId);
        }

        $this->targetLanguageAssoc[$languageCode][] = $fileTargetLang;
    }

    /**
     * @param      $languageCode
     * @param      $newLanguageCode
     * @param File $file
     * @param null $dqfId
     */
    public function modifyTargetLanguageToFile($languageCode, $newLanguageCode, File $file, $dqfId = null)
    {
        if (false === $this->hasFile($file)) {
            throw new \DomainException($file->getName() . ' does not belong to the project');
        }

        /** @var FileTargetLang $f */
        foreach ($this->getTargetLanguageAssoc()[$languageCode] as $k => $f) {
            if ($file->getName() === $f->getFile()->getName()) {
                unset($this->targetLanguageAssoc[$languageCode][$k]);

                if (count($this->targetLanguageAssoc[$languageCode]) === 0) {
                    unset($this->targetLanguageAssoc[$languageCode]);
                }
            }
        }

        $fileTargetLang = new FileTargetLang($newLanguageCode, $file);
        if ($dqfId) {
            $fileTargetLang->setDqfId($dqfId);
        }

        $this->targetLanguageAssoc[$newLanguageCode][] = $fileTargetLang;
    }

    /**
     * Clear the targetLanguageAssoc array
     */
    public function clearTargetLanguageAssoc()
    {
        $this->targetLanguageAssoc = [];
    }

    /**
     * @return array
     */
    public function getTargetLanguageAssoc()
    {
        return $this->targetLanguageAssoc;
    }

    /**
     * @param string $languageCode
     *
     * @return bool
     */
    public function hasTargetLanguage($languageCode)
    {
        return isset($this->targetLanguageAssoc[$languageCode]);
    }

    /**
     * @return Language[]
     */
    public function getTargetLanguages()
    {
        $targetLanguages = [];

        foreach (array_keys($this->targetLanguageAssoc) as $targetLanguage) {
            $targetLanguages[]  = new Language($targetLanguage);
        }

        return $targetLanguages;
    }

    /**
     * @return array
     */
    public function getSourceSegments()
    {
        return $this->sourceSegments;
    }

    /**
     * @param SourceSegment $sourceSegment
     */
    public function addSourceSegment(SourceSegment $sourceSegment)
    {
        if (false === $this->hasSourceSegment($sourceSegment)) {
            $this->sourceSegments[$sourceSegment->getFile()->getName()][] = $sourceSegment;
        }
    }

    /**
     * @param SourceSegment $sourceSegment
     *
     * @return bool
     */
    public function hasSourceSegment(SourceSegment $sourceSegment)
    {
        $fileName = $sourceSegment->getFile()->getName();

        if (empty($this->sourceSegments[$fileName])) {
            return false;
        }

        foreach ($this->sourceSegments[$fileName] as $segment) {
            if ($sourceSegment->isEqualTo($segment)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $fileName
     *
     * @return int
     */
    public function getSourceSegmentsCount($fileName)
    {
        return count($this->sourceSegments[$fileName]);
    }
}
