<?php

namespace Matecat\Dqf\Model\Entity;

use Matecat\Dqf\Cache\BasicAttributes;

class MasterProject extends AbstractProject
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
     * MasterProject constructor.
     *
     * @param string $name
     * @param string $sourceLanguageCode
     * @param int    $contentTypeId
     * @param int    $industryId
     * @param int    $processId
     * @param int    $qualityLevelId
     */
    public function __construct($name, $sourceLanguageCode, $contentTypeId, $industryId, $processId, $qualityLevelId)
    {
        $this->name = $name;
        $this->sourceLanguage = new Language($sourceLanguageCode);
        $this->setContentTypeId($contentTypeId);
        $this->setIndustryId($industryId);
        $this->setProcessId($processId);
        $this->setQualityLevelId($qualityLevelId);
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
        $allowed = [];
        $contentTypes = BasicAttributes::get('contentType');

        foreach ($contentTypes as $contentType) {
            $allowed[] = $contentType->id;
        }

        if (false === in_array($contentTypeId, $allowed)) {
            throw new \DomainException($contentTypeId . ' is not a valid value. [Allowed: '.implode(',', $allowed).']');
        }

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
        $allowed = [];
        $industries = BasicAttributes::get('industry');
        foreach ($industries as $industry) {
            $allowed[] = $industry->id;
        }

        if (false === in_array($industryId, $allowed)) {
            throw new \DomainException($industryId . ' is not a valid value. [Allowed: '.implode(',', $allowed).']');
        }

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
        $allowed = [];
        $processes = BasicAttributes::get('process');
        foreach ($processes as $process) {
            $allowed[] = $process->id;
        }

        if (false === in_array($processId, $allowed)) {
            throw new \DomainException($processId . ' is not a valid value. [Allowed: '.implode(',', $allowed).']');
        }

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
        $allowed = [];
        $qualitylevels = BasicAttributes::get('qualitylevel');
        foreach ($qualitylevels as $qualitylevel) {
            $allowed[] = $qualitylevel->id;
        }

        if (false === in_array($qualityLevelId, $allowed)) {
            throw new \DomainException($qualityLevelId . ' is not a valid value. [Allowed: '.implode(',', $allowed).']');
        }

        $this->qualityLevelId = $qualityLevelId;
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
     * @param string $fileName
     *
     * @return int
     */
    public function getSourceSegmentsCount($fileName)
    {
        return count($this->sourceSegments[$fileName]);
    }
}
