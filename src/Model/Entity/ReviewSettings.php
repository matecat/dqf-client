<?php

namespace Matecat\Dqf\Model\Entity;

class ReviewSettings extends BaseApiEntity
{
    /**
     * @var string
     */
    private $reviewType;

    /**
     * @var string
     */
    private $severityWeights;

    /**
     * @var int
     */
    private $errorCategoryIds0;

    /**
     * @var int
     */
    private $errorCategoryIds1;

    /**
     * @var int
     */
    private $errorCategoryIds2;

    /**
     * @var float
     */
    private $passFailThreshold;

    /**
     * @return string
     */
    public function getReviewType()
    {
        return $this->reviewType;
    }

    /**
     * @param string $reviewType
     */
    public function setReviewType($reviewType)
    {
        $allowed = ['correction', 'error_typology', 'combined'];

        if (false === in_array($reviewType, $allowed)) {
            throw new \DomainException($reviewType . 'is not a valid reviewType. [Allowed: '.implode(',', $allowed).']');
        }

        $this->reviewType = $reviewType;
    }

    /**
     * @return string
     */
    public function getSeverityWeights()
    {
        return $this->severityWeights;
    }

    /**
     * @param string $severityWeights
     */
    public function setSeverityWeights($severityWeights)
    {
        $this->severityWeights = $severityWeights;
    }

    /**
     * @return int
     */
    public function getErrorCategoryIds0()
    {
        return $this->errorCategoryIds0;
    }

    /**
     * @param int $errorCategoryIds0
     */
    public function setErrorCategoryIds0($errorCategoryIds0)
    {
        $this->errorCategoryIds0 = $errorCategoryIds0;
    }

    /**
     * @return int
     */
    public function getErrorCategoryIds1()
    {
        return $this->errorCategoryIds1;
    }

    /**
     * @param int $errorCategoryIds1
     */
    public function setErrorCategoryIds1($errorCategoryIds1)
    {
        $this->errorCategoryIds1 = $errorCategoryIds1;
    }

    /**
     * @return int
     */
    public function getErrorCategoryIds2()
    {
        return $this->errorCategoryIds2;
    }

    /**
     * @param int $errorCategoryIds2
     */
    public function setErrorCategoryIds2($errorCategoryIds2)
    {
        $this->errorCategoryIds2 = $errorCategoryIds2;
    }

    /**
     * @return float
     */
    public function getPassFailThreshold()
    {
        return $this->passFailThreshold;
    }

    /**
     * @param float $passFailThreshold
     */
    public function setPassFailThreshold($passFailThreshold)
    {
        $this->passFailThreshold = $passFailThreshold;
    }
}
