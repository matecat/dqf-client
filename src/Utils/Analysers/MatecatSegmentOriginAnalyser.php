<?php

namespace Matecat\Dqf\Utils\Analysers;

class MatecatSegmentOriginAnalyser implements SegmentOriginAnalyserInterface
{
    /**
     * @param array $row
     *
     * @return array
     */
    public static function analyse(array $row = [])
    {
        // @TODO if the user started the translation from scratch is HT
        // maybe check for $row['from_scratch'] or something like that??

        // if there are no suggestion array return HT
        if (empty($row['suggestions_array'])) {
            return [
                'segment_origin' => 'HT',
                'suggestion_match' => null
            ];
        }

        $data = self::assignDefaultValues($row);

        if (self::didTheUserModifyTheSegment($row)) {

            $suggestion_position  = $row[ 'suggestion_position' ];
            $suggestions = json_decode($row[ 'suggestions_array' ], true);

            if (self::didTheUserChooseASuggestion($suggestions, $suggestion_position)) {
                $selected = $suggestions[$suggestion_position];

                // if the suggestion was created by a MT
                if (strpos( $selected[ 'created_by' ], 'MT' ) !== false) {
                    return [
                        'segment_origin' => 'MT',
                        'suggestion_match' => null
                    ];
                }

                // otherwise, we consider it a TM
                return [
                    'segment_origin' => 'TM',
                    'suggestion_match' => self::getMatch($selected[ 'match' ])
                ];
            }
        }

        return $data;
    }

    /**
     * @param array $row
     *
     * @return array
     */
    private static function assignDefaultValues( $row)
    {
        if ((strpos($row['match_type'], '100%') === 0) or $row['match_type'] === 'ICE' or $row['match_type'] === 'REPETITIONS') {
            return [
                'segment_origin' =>   'TM',
                'suggestion_match' => '100'
            ];
        }

        if (strpos($row[ 'match_type' ], '%') !== false) {
            return [
                'segment_origin' =>   'TM',
                'suggestion_match' => $row['suggestion_match']
            ];
        }

        return [
            'segment_origin' =>   'MT',
            'suggestion_match' => null
        ];
    }

    /**
     * @param string $match
     *
     * @return string
     */
    private static function getMatch($match)
    {
        return str_replace('%', '', $match);
    }

    /**
     * @param array $row
     *
     * @return bool
     */
    private static function didTheUserModifyTheSegment($row)
    {
        return (!is_null($row['suggestion']) and $row['translation'] !== $row['suggestion']);
    }

    /**
     * @param array $suggestions
     * @param null $suggestion_position
     *
     * @return bool
     */
    private static function didTheUserChooseASuggestion($suggestions, $suggestion_position = null)
    {
        if(null !== $suggestion_position){
            return isset($suggestions[$suggestion_position]);
        }

        return false;
    }
}
