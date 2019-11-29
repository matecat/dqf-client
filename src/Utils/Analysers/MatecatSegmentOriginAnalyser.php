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
        if (empty($row['suggestions_array'])) {
            return [
                    'segment_origin' => 'HT',
                    'suggestion_match' => null
            ];
        }

        $data = [];

        if ((strpos($row[ 'match_type' ], '100%') === 0) or $row[ 'match_type' ] == 'ICE' or $row[ 'match_type' ] == 'REPETITIONS') {
            $data[ 'segment_origin' ]   = 'TM';
            $data[ 'suggestion_match' ] = '100';
        } elseif (strpos($row[ 'match_type' ], '%') !== false) {
            $data[ 'segment_origin' ]   = 'TM';
            $data[ 'suggestion_match' ] = $row[ 'suggestion_match' ];
        } elseif ($row[ 'match_type' ] == 'MT') {
            $data[ 'segment_origin' ] = 'MT';
            $data[ 'suggestion_match' ] = null;
        }

        if (self::didTheUserModifyTheSegment($row)) {
            $suggestion_position  = $row[ 'suggestion_position' ];
            $suggestions = json_decode($row[ 'suggestions_array' ], true);

            if (isset($suggestions[ $suggestion_position ])) {
                $selected = $suggestions[ $suggestion_position ];

                if ($selected[ 'created_by' ] === 'MT!' or $selected[ 'created_by' ] === 'MT') {
                    return [
                        'segment_origin' => 'MT',
                        'suggestion_match' => null
                    ];
                }

                return [
                        'segment_origin' => 'TM',
                        'suggestion_match' => self::getMatch($selected[ 'match' ])
                ];
            }
        }

        return $data;
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
        return (!is_null($row[ 'suggestion' ]) and $row[ 'translation' ] !== $row[ 'suggestion' ]);
    }
}
