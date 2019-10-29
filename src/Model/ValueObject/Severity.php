<?php

namespace Matecat\Dqf\Model\ValueObject;

use Matecat\Dqf\Cache\BasicAttributes;

class Severity
{
    /**
     * @var int
     */
    private $severityId;

    /**
     * @var int
     */
    private $weight;

    /**
     * Severity constructor.
     *
     * @param int $severityId
     * @param int $weight
     */
    public function __construct($severityId, $weight)
    {
        $this->setSeverityId($severityId);
        $this->weight = $weight;
    }

    /**
     * @param $severityId
     */
    private function setSeverityId($severityId)
    {
        $allowed = [];
        $severities = BasicAttributes::get('severity');
        foreach ($severities as $severity) {
            $allowed[] = $severity->id;
        }

        if (false === in_array($severityId, $allowed)) {
            throw new \DomainException($severityId . ' is not a valid value. [Allowed: '.implode(',', $allowed).']');
        }

        $this->severityId = $severityId;
    }

    /**
     * @return int
     */
    public function getSeverityId()
    {
        return $this->severityId;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }
}
