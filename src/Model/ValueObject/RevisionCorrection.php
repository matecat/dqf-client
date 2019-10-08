<?php

namespace Matecat\Dqf\Model\ValueObject;

class RevisionCorrection
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var int
     */
    private $time;

    /**
     * @var RevisionCorrectionItem[]
     */
    private $detailList;

    /**
     * RevisionCorrection constructor.
     *
     * @param string $content
     * @param int    $time
     */
    public function __construct($content, $time)
    {
        $this->content = $content;
        $this->time    = $time;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param RevisionCorrectionItem $item
     */
    public function addItem(RevisionCorrectionItem $item)
    {
        if (false === $this->hasItem($item)) {
            $this->detailList[] = $item;
        }
    }

    /**
     * @param RevisionCorrectionItem $item
     *
     * @return bool
     */
    public function hasItem(RevisionCorrectionItem $item)
    {
        if (empty($this->detailList)) {
            return false;
        }

        foreach ($this->detailList as $i) {
            if ($i->isEqualTo($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return RevisionCorrectionItem[]
     */
    public function getDetailList()
    {
        return $this->detailList;
    }
}
