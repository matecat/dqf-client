<?php

namespace Matecat\Dqf\Model\Entity;

class TranslatedSegment extends BaseApiEntity
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
     * @var SourceSegment
     */
    private $sourceSegment;

    /**
     * @var string
     */
    private $targetSegment;

    /**
     * @var string
     */
    private $editedSegment;

    /**
     * @var int
     */
    private $time;

    /**
     * @var int
     */
    private $segmentOriginId;

    /**
     * @var float
     */
    private $matchRate;

    /**
     * @var int
     */
    private $mtEngineId;

    /**
     * @var string
     */
    private $mtEngineOtherName;

    /**
     * @var int
     */
    private $mtEngineVersion;

    /**
     * @var string
     */
    private $segmentOriginDetail;

    /**
     * TranslatedSegment constructor.
     *
     * @param ChildProject  $childProject
     * @param File          $file
     * @param string        $targetLanguageCode
     * @param SourceSegment $sourceSegment
     * @param string        $targetSegment
     * @param string        $editedSegment
     */
    public function __construct(ChildProject $childProject, File $file, $targetLanguageCode, SourceSegment $sourceSegment, $targetSegment, $editedSegment)
    {
        $this->childProject   = $childProject;
        $this->file           = $file;
        $this->targetLanguage = new Language($targetLanguageCode);
        $this->sourceSegment  = $sourceSegment;
        $this->targetSegment  = $targetSegment;
        $this->editedSegment  = $editedSegment;
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
     * @return SourceSegment
     */
    public function getSourceSegment()
    {
        return $this->sourceSegment;
    }

    /**
     * @return string
     */
    public function getTargetSegment()
    {
        return $this->targetSegment;
    }

    /**
     * @return string
     */
    public function getEditedSegment()
    {
        return $this->editedSegment;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getSegmentOriginId()
    {
        return $this->segmentOriginId;
    }

    /**
     * @param int $segmentOriginId
     */
    public function setSegmentOriginId($segmentOriginId)
    {
        $this->segmentOriginId = $segmentOriginId;
    }

    /**
     * @return float
     */
    public function getMatchRate()
    {
        return $this->matchRate;
    }

    /**
     * @param float $matchRate
     */
    public function setMatchRate($matchRate)
    {
        $this->matchRate = $matchRate;
    }

    /**
     * @return int
     */
    public function getMtEngineId()
    {
        return $this->mtEngineId;
    }

    /**
     * @param int $mtEngineId
     */
    public function setMtEngineId($mtEngineId)
    {
        $this->mtEngineId = $mtEngineId;
    }

    /**
     * @return string
     */
    public function getMtEngineOtherName()
    {
        return $this->mtEngineOtherName;
    }

    /**
     * @param string $mtEngineOtherName
     */
    public function setMtEngineOtherName($mtEngineOtherName)
    {
        $this->mtEngineOtherName = $mtEngineOtherName;
    }

    /**
     * @return int
     */
    public function getMtEngineVersion()
    {
        return $this->mtEngineVersion;
    }

    /**
     * @param int $mtEngineVersion
     */
    public function setMtEngineVersion($mtEngineVersion)
    {
        $this->mtEngineVersion = $mtEngineVersion;
    }

    /**
     * @return string
     */
    public function getSegmentOriginDetail()
    {
        return $this->segmentOriginDetail;
    }

    /**
     * @param string $segmentOriginDetail
     */
    public function setSegmentOriginDetail($segmentOriginDetail)
    {
        $this->segmentOriginDetail = $segmentOriginDetail;
    }
}
