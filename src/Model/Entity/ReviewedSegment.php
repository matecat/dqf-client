<?php

namespace Matecat\Dqf\Model\Entity;

use Matecat\Dqf\Model\ValueObject\RevisionCorrection;
use Matecat\Dqf\Model\ValueObject\RevisionError;

class ReviewedSegment extends BaseApiEntity
{
    /**
     * @var string
     */
    private $comment;

    /**
     * @var RevisionError[]
     */
    private $errors;

    /**
     * @var RevisionCorrection
     */
    private $correction;

    public function __construct($comment = null)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return RevisionError[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param RevisionError $error
     */
    public function addError(RevisionError $error)
    {
        if (false === $this->hasError($error)) {
            $this->errors[] = $error;
        }
    }

    /**
     * @param RevisionError $error
     *
     * @return bool
     */
    public function hasError(RevisionError $error)
    {
        if (empty($this->errors)) {
            return false;
        }

        foreach ($this->errors as $e) {
            if ($e->isEqualTo($error)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return RevisionCorrection
     */
    public function getCorrection()
    {
        return $this->correction;
    }

    /**
     * @param RevisionCorrection $correction
     */
    public function setCorrection(RevisionCorrection $correction)
    {
        $this->correction = $correction;
    }
}
