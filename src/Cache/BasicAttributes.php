<?php

namespace Matecat\Dqf\Cache;

use Matecat\Dqf\Client;
use Matecat\Dqf\Exceptions\CacheException;

class BasicAttributes
{
    private static $dataPath;

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
        return (array)json_decode(file_get_contents(self::getDataFile()), false);
    }

    /**
     * @param string $key
     * @param int    $id
     *
     * @return bool
     */
    public static function existsById($key, $id)
    {
        return self::getFromId($key, $id) ? true : false;
    }

    /**
     * @param string $key
     * @param string $name
     *
     * @return bool
     */
    public static function existsByName($key, $name)
    {
        return self::getFromName($key, $name) ? true : false;
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
     * @param string $key
     * @param int $id
     *
     * @return mixed
     */
    public static function getFromId($key, $id)
    {
        $data = self::get($key);

        foreach ($data as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }
    }

    /**
     * @param string $key
     * @param string $name
     *
     * @return mixed
     */
    public static function getFromName($key, $name)
    {
        $data = self::get($key);

        foreach ($data as $item) {
            if ($item->name === $name) {
                return $item;
            }
        }
    }

    /**
     * @param Client $client
     *
     * @throws CacheException
     */
    public static function refresh(Client $client)
    {
        $aggregate = $client->getBasicAttributesAggregate([]);

        try {
            file_put_contents(self::getDataFile(), json_encode($aggregate, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            throw new CacheException('File ' . self::getDataFile() . ' cannot be created. Be sure that directory is writable.');
        }
    }

    /**
     * @param string $dataPath
     */
    public static function setDataFile($dataPath)
    {
        self::$dataPath = $dataPath;
    }

    /**
     * @return string
     */
    public static function getDataFile()
    {
        return (isset(self::$dataPath)) ? self::$dataPath : __DIR__.'/data/attributes.json';
    }
}
