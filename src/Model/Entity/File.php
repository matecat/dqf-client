<?php

namespace Matecat\Dqf\Model\Entity;

class File extends BaseApiEntity
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $numberOfSegments;

    /**
     * @var int
     */
    private $tmsFileId;

    /**
     * File constructor.
     *
     * @param $name
     * @param $numberOfSegments
     */
    public function __construct($name, $numberOfSegments)
    {
        $this->name = $name;
        $this->numberOfSegments = $numberOfSegments;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getNumberOfSegments()
    {
        return $this->numberOfSegments;
    }

    /**
     * @param int $numberOfSegments
     */
    public function setNumberOfSegments($numberOfSegments)
    {
        $this->numberOfSegments = $numberOfSegments;
    }

    /**
     * @return int
     */
    public function getTmsFileId()
    {
        return $this->tmsFileId;
    }

    /**
     * @param int $tmsFileId
     */
    public function setTmsFileId($tmsFileId)
    {
        $this->tmsFileId = $tmsFileId;
    }
}
