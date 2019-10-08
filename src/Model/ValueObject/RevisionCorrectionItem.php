<?php

namespace Matecat\Dqf\Model\ValueObject;

class RevisionCorrectionItem
{
    /**
     * @var string
     */
    private $subContent;

    /**
     * @var string
     */
    private $type;

    /**
     * RevisionCorrectionItem constructor.
     *
     * @param string $subContent
     * @param string $type
     */
    public function __construct($subContent, $type)
    {
        $this->subContent = $subContent;
        $this->setType($type);
    }

    /**
     * @param string $type
     */
    private function setType($type)
    {
        $allowed = ['unchanged','added', 'deleted'];

        if (false === in_array($type, $allowed)) {
            throw new \DomainException($type . 'is not a valid type. [Allowed: '.implode(',', $allowed).']');
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getSubContent()
    {
        return $this->subContent;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param RevisionCorrectionItem $revisionCorrectionItem
     *
     * @return bool
     */
    public function isEqualTo(RevisionCorrectionItem $revisionCorrectionItem)
    {
        return (
                $this->subContent === $revisionCorrectionItem->getSubContent() and
                $this->type === $revisionCorrectionItem->getType()
        );
    }
}
