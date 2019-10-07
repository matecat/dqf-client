<?php

namespace Matecat\Dqf\Model\Entity;

class SourceSegment extends BaseApiEntity
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $segment;

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param int $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
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
}
