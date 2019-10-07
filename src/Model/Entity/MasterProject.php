<?php

namespace Matecat\Dqf\Model\Entity;

class MasterProject extends BaseApiEntity
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
     * @var ReviewSettings
     */
    private $reviewSettings;

    /**
     * @var Template
     */
    private $template;

    /**
     * @var File[]
     */
    private $files;

    /**
     * @var SourceSegment[]
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
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param Template $template
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }

    /**
     * @return File[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    public function hasFile(File $file)
    {
        if(empty($this->getFiles())){
            return false;
        }

        foreach ($this->getFiles() as $f){
            if($file->getName() === $f->getName()){
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
        if(false === $this->hasFile($file)){
            $this->files[] = $file;
        }
    }

    /**
     * @param string $languageCode
     * @param File     $file
     */
    public function assocTargetLanguageToFile($languageCode, File $file)
    {
        if(false === $this->hasFile($file)){
            throw new \DomainException($file->getName() . ' does not belong to the project');
        }

        $this->targetLanguageAssoc[$languageCode][] = $file;
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

        foreach (array_keys($this->targetLanguageAssoc) as $targetLanguage){
            $targetLanguages[]  = new Language($targetLanguage);
        }

        return $targetLanguages;
    }

    /**
     * @return SourceSegment[]
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
        if (false === $this->hasSourceSegment($sourceSegment->getIndex())) {
            $this->sourceSegments[$sourceSegment->getIndex()] = $sourceSegment;
        }
    }

    /**
     * @param int $sourceSegmentIndex
     *
     * @return bool
     */
    public function hasSourceSegment($sourceSegmentIndex)
    {
        return isset($this->sourceSegments[$sourceSegmentIndex]);
    }

    /**
     * @return int
     */
    public function getSourceSegmentsCount()
    {
        return count($this->sourceSegments);
    }
}
