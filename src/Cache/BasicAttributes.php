<?php

namespace Matecat\Dqf\Cache;

use Matecat\Dqf\Client;

class BasicAttributes
{
    /**
     * Keys complete list
     */
    const LANGUAGE = 'language';
    const SEVERITY = 'severity';
    const MT_ENGINE = 'mtEngine';
    const PROCESS = 'process';
    const CONTENT_TYPE = 'contentType';
    const SEGMENT_ORIGIN = 'segmentOrigin';
    const CAT_TOOL = 'catTool';
    const INDUSTRY = 'industry';
    const ERROR_CATEGORY = 'errorCategory';
    const QUALITY_LEVEL = 'qualitylevel';

    /**
     * @return array
     */
    public static function all()
    {
        return (array)json_decode(file_get_contents(self::getDataFile()));
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public static function get($key)
    {
        $data = self::all();

        return (isset($data[$key])) ? $data[$key] : null;
    }

    /**
     * @param Client $client
     */
    public static function refresh(Client $client)
    {
        $aggregate = $client->getBasicAttributesAggregate([]);

        file_put_contents(self::getDataFile(), json_encode($aggregate));
    }

    /**
     * @return string
     */
    private static function getDataFile()
    {
        return __DIR__.'/data/attributes.json';
    }
}