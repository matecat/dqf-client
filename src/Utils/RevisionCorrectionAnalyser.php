<?php

namespace Matecat\Dqf\Utils;

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
     *
     * @param string $old
     * @param string $new
     *
     * @return array
     */
    public static function analyse($old, $new)
    {
        $results = [];

        $oldExpl = preg_split('/\s/', $old);
        $newExpl = preg_split('/\s/', $new);

        $countNewExpl = count($newExpl);
        $countOldExpl = count($oldExpl);

        // added
        for ($i = 0; $i < $countNewExpl; $i++) {

            if( isset($newExpl[$i]) and false === in_array($newExpl[$i], $oldExpl) ){

                $word = $newExpl[$i];

                for ($k = 1; $k <= $countNewExpl; $k++) {
                    if( isset($newExpl[$i+$k]) and false === in_array($newExpl[$i+$k], $oldExpl) ){
                        $word .= ' ' . $newExpl[$i+$k];
                        unset($newExpl[$i+$k]);
                    } else {
                        break;
                    }
                }

                $results[$word] = 'added';
            }
        }

        // unchanged
        for ($i = 0; $i < $countNewExpl; $i++) {

            if( isset($newExpl[$i]) and true === in_array($newExpl[$i], $oldExpl) ){

                $word = $newExpl[$i];

                for ($k = 1; $k <= $countNewExpl; $k++) {
                    if( isset($newExpl[$i+$k]) and true === in_array($newExpl[$i+$k], $oldExpl) ){
                        $word .= ' ' . $newExpl[$i+$k];
                        unset($newExpl[$i+$k]);
                    } else {
                        break;
                    }
                }

                $results[$word] = 'unchanged';
            }
        }

        // deleted
        for ($i = 0; $i < $countOldExpl; $i++) {

            $newExpl = preg_split('/\s/', $new);

            if( isset($oldExpl[$i]) and false === in_array($oldExpl[$i], $newExpl) ){

                $word = $oldExpl[$i];

                for ($k = 1; $k <= $countOldExpl; $k++) {
                    if( isset($oldExpl[$i+$k]) and false === in_array($oldExpl[$i+$k], $newExpl) ){
                        $word .= ' ' . $oldExpl[$i+$k];
                        unset($oldExpl[$i+$k]);
                    } else {
                        break;
                    }
                }

                $results[$word] = 'deleted';
            }

        }

        return $results;
    }
}