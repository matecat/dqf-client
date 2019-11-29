<?php

namespace Matecat\Dqf\Utils\Analysers;

interface SegmentOriginAnalyserInterface
{
    /**
     * @param array $row
     *
     * @return array
     */
    public static function analyse(array $row = []);
}
