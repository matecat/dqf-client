<?php

namespace Matecat\Dqf\Model\Entity;

use Matecat\Dqf\Cache\BasicAttributes;

class TranslatedSegment extends BaseApiEntity
{
    /**
     * @var Language
     */
    private $targetLanguage;

    /**
     * @var int
     */
    private $sourceSegmentId;

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
     * @var int
     */
    private $indexNo;

    /**
     * TranslatedSegment constructor.
     *
     * @param int      $mtEngineId
     * @param int      $segmentOriginId
     * @param string   $targetLanguageCode
     * @param int      $sourceSegmentId
     * @param string   $targetSegment
     * @param string   $editedSegment
     * @param int      $indexNo
     */
    public function __construct(
        $mtEngineId,
        $segmentOriginId,
        $targetLanguageCode,
        $sourceSegmentId,
        $targetSegment,
        $editedSegment,
        $indexNo
    ) {
        $this->setMtEngineId($mtEngineId);
        $this->setSegmentOriginId($segmentOriginId);
        $this->targetLanguage = new Language($targetLanguageCode);
        $this->sourceSegmentId  = $sourceSegmentId;
        $this->targetSegment  = $targetSegment;
        $this->editedSegment  = $editedSegment;
        $this->indexNo  = $indexNo;
    }

    /**
     * @return Language
     */
    public function getTargetLanguage()
    {
        return $this->targetLanguage;
    }

    /**
     * @param Language $targetLanguage
     */
    public function setTargetLanguage($targetLanguage)
    {
        $this->targetLanguage = $targetLanguage;
    }

    /**
     * @return int
     */
    public function getSourceSegmentId()
    {
        return $this->sourceSegmentId;
    }

    /**
     * @return string
     */
    public function getTargetSegment()
    {
        return $this->targetSegment;
    }

    /**
     * @param string $targetSegment
     */
    public function setTargetSegment($targetSegment)
    {
        $this->targetSegment = $targetSegment;
    }

    /**
     * @return string
     */
    public function getEditedSegment()
    {
        return $this->editedSegment;
    }

    /**
     * @param string $editedSegment
     */
    public function setEditedSegment($editedSegment)
    {
        $this->editedSegment = $editedSegment;
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
        $allowed = [];
        $segmentOrigins = BasicAttributes::get('segmentOrigin');
        foreach ($segmentOrigins as $segmentOrigin) {
            $allowed[] = $segmentOrigin->id;
        }

        if (false === in_array($segmentOriginId, $allowed)) {
            throw new \DomainException($segmentOriginId . ' is not an allowed value. [Allowed: '.implode(',', $allowed).']');
        }

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
        $allowed = [];
        $mtEngines = BasicAttributes::get('mtEngine');
        foreach ($mtEngines as $mtEngine) {
            $allowed[] = $mtEngine->id;
        }

        if (false === in_array($mtEngineId, $allowed)) {
            throw new \DomainException($mtEngineId . ' is not a valid value. [Allowed: '.implode(',', $allowed).']');
        }

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

    /**
     * @return int
     */
    public function getIndexNo()
    {
        return $this->indexNo;
    }

    /**
     * @param int $indexNo
     */
    public function setIndexNo($indexNo)
    {
        $this->indexNo = $indexNo;
    }
}
