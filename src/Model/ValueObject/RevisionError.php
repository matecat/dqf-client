<?php

namespace Matecat\Dqf\Model\ValueObject;

class RevisionError
{
    /**
     * @var int
     */
    private $errorCategoryId;

    /**
     * @var int
     */
    private $severityId;

    /**
     * @var int
     */
    private $charPosStart;

    /**
     * @var int
     */
    private $charPosEnd;

    /**
     * @var bool
     */
    private $isRepeated;

    /**
     * RevisionError constructor.
     *
     * @param int  $errorCategoryId
     * @param int  $severityId
     * @param null $charPosStart
     * @param null $charPosEnd
     * @param bool $isRepeated
     */
    public function __construct($errorCategoryId, $severityId, $charPosStart = null, $charPosEnd = null, $isRepeated = false)
    {
        $this->errorCategoryId = $errorCategoryId;
        $this->severityId      = $severityId;

        if ($charPosEnd !== null and $charPosStart > $charPosEnd) {
            throw new \DomainException('\'charPosStart\' cannot be greater than \'charPosEnd\'');
        }

        $this->charPosStart    = $charPosStart;
        $this->charPosEnd      = $charPosEnd;
        $this->isRepeated      = $isRepeated;
    }

    /**
     * @return int
     */
    public function getErrorCategoryId()
    {
        return $this->errorCategoryId;
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
    public function getCharPosStart()
    {
        return $this->charPosStart;
    }

    /**
     * @return int
     */
    public function getCharPosEnd()
    {
        return $this->charPosEnd;
    }

    /**
     * @return bool
     */
    public function isRepeated()
    {
        return $this->isRepeated;
    }

    /**
     * @param RevisionError $revisionError
     *
     * @return bool
     */
    public function isEqualTo(RevisionError $revisionError)
    {
        return (
                $this->errorCategoryId === $revisionError->getErrorCategoryId() and
                $this->severityId === $revisionError->getSeverityId() and
                $this->charPosStart === $revisionError->getCharPosStart() and
                $this->charPosEnd === $revisionError->getCharPosEnd() and
                $this->isRepeated === $revisionError->isRepeated()
        );
    }
}
