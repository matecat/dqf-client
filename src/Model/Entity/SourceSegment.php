<?php

namespace Matecat\Dqf\Model\Entity;

class SourceSegment extends BaseApiEntity
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $segment;

    /**
     * SourceSegment constructor.
     *
     * @param File   $file
     * @param int    $index
     * @param null   $segment
     */
    public function __construct(File $file, $index, $segment = null)
    {
        $this->file    = $file;
        $this->index   = $index;
        $this->segment = $segment;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @param string $segment
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;
    }

    /**
     * @param SourceSegment $sourceSegment
     *
     * @return bool
     */
    public function isEqualTo(SourceSegment $sourceSegment)
    {
        return (
            $this->getFile()->getName() === $sourceSegment->getFile()->getName() and
            $this->getIndex() === $sourceSegment->getIndex() and
            $this->getSegment() === $sourceSegment->getSegment()
        );
    }
}
