<?php

namespace Matecat\Dqf\Utils\Analysers;

use Matecat\Dqf\Constants;

class RevisionCorrectionAnalyser
{
    /**
     * This function compares two strings and returns the differences.
     *
     * Example:
     *
     * $old = 'Test Segment';
     * $new = 'Some in Test and also added.';
     *
     * RevisionCorrectionAnalyser::analyse($old, $new) will returns this array:
     *
     * [
     *     'Some in'         => 'added',
     *     'Test'            => 'unchanged',
     *     'Segment'         => 'deleted',
     *     'and also added.' => 'added',
     * ]
     *
     * @param string $old
     * @param string $new
     *
     * @return array
     */
    public static function analyse($old, $new)
    {
        $results = [];

        $oldWords = self::explodeStringInWords($old);
        $newWords = self::explodeStringInWords($new);

        $countNewWords = count($newWords);
        $countOldWords = count($oldWords);

        // append 'added'
        for ($i = 0; $i < $countNewWords; $i++) {
            if (isset($newWords[$i]) and false === in_array($newWords[$i], $oldWords)) {
                $word = $newWords[$i];

                for ($k = 1; $k <= $countNewWords; $k++) {
                    if (isset($newWords[$i+$k]) and false === in_array($newWords[$i+$k], $oldWords)) {
                        $word .= ' ' . $newWords[$i+$k];
                        unset($newWords[$i+$k]);
                    } else {
                        break;
                    }
                }

                $results[$word] = Constants::REVISION_CORRECTION_TYPE_ADDED;
            }
        }

        // append 'unchanged'
        for ($i = 0; $i < $countNewWords; $i++) {
            if (isset($newWords[$i]) and true === in_array($newWords[$i], $oldWords)) {
                $word = $newWords[$i];

                for ($k = 1; $k <= $countNewWords; $k++) {
                    if (isset($newWords[$i+$k]) and true === in_array($newWords[$i+$k], $oldWords)) {
                        $word .= ' ' . $newWords[$i+$k];
                        unset($newWords[$i+$k]);
                    } else {
                        break;
                    }
                }

                $results[$word] = Constants::REVISION_CORRECTION_TYPE_UNCHANGED;
            }
        }

        // append 'deleted'
        for ($i = 0; $i < $countOldWords; $i++) {
            $newWords = self::explodeStringInWords($new);

            if (isset($oldWords[$i]) and false === in_array($oldWords[$i], $newWords)) {
                $word = $oldWords[$i];

                for ($k = 1; $k <= $countOldWords; $k++) {
                    if (isset($oldWords[$i+$k]) and false === in_array($oldWords[$i+$k], $newWords)) {
                        $word .= ' ' . $oldWords[$i+$k];
                        unset($oldWords[$i+$k]);
                    } else {
                        break;
                    }
                }

                $results[$word] = Constants::REVISION_CORRECTION_TYPE_DELETED;
            }
        }

        return $results;
    }

    /**
     * @param string $string
     *
     * @return array[]|false|string[]
     */
    private static function explodeStringInWords($string)
    {
        return preg_split('/(\s+)/', $string);
    }
}
