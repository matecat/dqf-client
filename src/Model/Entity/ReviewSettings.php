<?php

namespace Matecat\Dqf\Model\Entity;

use Matecat\Dqf\Constants;
use Matecat\Dqf\Model\ValueObject\Severity;

class ReviewSettings extends BaseApiEntity
{
    /**
     * @var string
     */
    private $reviewType;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var array
     */
    private $severityWeights;

    /**
     * @var array
     */
    private $errorCategoryIds;

    /**
     * @var float
     */
    private $passFailThreshold;

    /**
     * @var float
     */
    private $sampling;

    /**
     * ReviewSettings constructor.
     *
     * @param string $reviewType
     */
    public function __construct($reviewType)
    {
        $this->setReviewType($reviewType);
    }

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
    private function setReviewType($reviewType)
    {
        $allowed = [Constants::REVIEW_TYPE_CORRECTION, Constants::REVIEW_TYPE_ERROR, Constants::REVIEW_TYPE_COMBINED];

        if (false === in_array($reviewType, $allowed)) {
            throw new \DomainException($reviewType . 'is not a valid reviewType. [Allowed: '.implode(',', $allowed).']');
        }

        $this->reviewType = $reviewType;
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
    public function getSeverityWeights()
    {
        $severities = [];

        /** @var Severity $severityWeight */
        foreach ($this->severityWeights as $severityWeight) {
            $severities[] = [
                    'severityId' => $severityWeight->getSeverityId(),
                    'weight' => $severityWeight->getWeight(),
            ];
        }

        return json_encode($severities);
    }

    /**
     * @param Severity $severityWeight
     */
    public function addSeverityWeight(Severity $severityWeight)
    {
        $this->severityWeights[] = $severityWeight;
    }

    /**
     * @return array
     */
    public function getErrorCategoryIds()
    {
        return $this->errorCategoryIds;
    }

    /**
     * @param $errorCategoryId
     */
    public function addErrorCategoryId($errorCategoryId)
    {
        $this->validateErrorCategoryId($errorCategoryId);
        $this->errorCategoryIds[] = $errorCategoryId;
    }

    /**
     * @param int $errorCategoryId
     */
    private function validateErrorCategoryId($errorCategoryId)
    {
        $allowed = [1, 2, 3, 4, 5, 6, 7, 8];

        if (false === in_array($errorCategoryId, $allowed)) {
            throw new \DomainException($errorCategoryId . ' is not a valid value. [Allowed: '.implode(',', $allowed).']');
        }
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

    /**
     * @return float
     */
    public function getSampling()
    {
        return $this->sampling;
    }

    /**
     * @param float $sampling
     */
    public function setSampling($sampling)
    {
        $this->sampling = $sampling;
    }
}
