<?php

namespace Matecat\Dqf\Model\Entity;

class Template extends BaseApiEntity
{
    /**
     * @var string
     */
    private $name;

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
     * @var bool
     */
    private $isPublic;

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
     * @return bool
     */
    public function isPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
    }
}
