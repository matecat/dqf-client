<?php

namespace Matecat\Dqf\Tests\Cache;

use Matecat\Dqf\Cache\BasicAttributes;
use Matecat\Dqf\Exceptions\CacheException;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Tests\BaseTest;

class BasicAttributesTest extends BaseTest
{
    /**
     * @test
     */
    public function all()
    {
        $all = BasicAttributes::all();

        $this->assertArrayHasKey(BasicAttributes::LANGUAGE, $all);
        $this->assertArrayHasKey(BasicAttributes::SEVERITY, $all);
        $this->assertArrayHasKey(BasicAttributes::MT_ENGINE, $all);
        $this->assertArrayHasKey(BasicAttributes::PROCESS, $all);
        $this->assertArrayHasKey(BasicAttributes::CONTENT_TYPE, $all);
        $this->assertArrayHasKey(BasicAttributes::SEGMENT_ORIGIN, $all);
        $this->assertArrayHasKey(BasicAttributes::CAT_TOOL, $all);
        $this->assertArrayHasKey(BasicAttributes::INDUSTRY, $all);
        $this->assertArrayHasKey(BasicAttributes::ERROR_CATEGORY, $all);
        $this->assertArrayHasKey(BasicAttributes::QUALITY_LEVEL, $all);
    }

    /**
     * @test
     */
    public function get_null()
    {
        $null = BasicAttributes::get('fdsfdsfds');

        $this->assertNull($null);
    }

    /**
     * @test
     */
    public function get_languages()
    {
        $languages = BasicAttributes::get(BasicAttributes::LANGUAGE);
        $first = $languages[0];

        $this->assertNotNull($first);
        $this->assertEquals($first->id, 1);
        $this->assertEquals($first->localeCode, 'af-ZA');
        $this->assertEquals($first->name, 'Afrikaans(South Africa)');
    }

    /**
     * @test
     */
    public function get_severity()
    {
        $severity = BasicAttributes::get(BasicAttributes::SEVERITY);
        $first = $severity[0];

        $this->assertEquals($first->id, 1);
        $this->assertEquals($first->defaultValue, 1);
        $this->assertEquals($first->name, 'neutral');
    }

    /**
     * @test
     */
    public function get_mtEngine()
    {
        $mtEngine = BasicAttributes::get(BasicAttributes::MT_ENGINE);
        $first = $mtEngine[0];

        $this->assertEquals($first->id, 2);
        $this->assertEquals($first->name, 'Apertium');
    }

    /**
     * @test
     */
    public function get_process()
    {
        $process = BasicAttributes::get(BasicAttributes::PROCESS);
        $first = $process[0];

        $this->assertEquals($first->id, 5);
        $this->assertEquals($first->name, 'HT');
    }

    /**
     * @test
     */
    public function get_contentType()
    {
        $contentType = BasicAttributes::get(BasicAttributes::CONTENT_TYPE);
        $first = $contentType[0];

        $this->assertEquals($first->id, 1);
        $this->assertEquals($first->name, 'User Interface Text');
    }

    /**
     * @test
     */
    public function get_segmentOrigin()
    {
        $segmentOrigin = BasicAttributes::get(BasicAttributes::SEGMENT_ORIGIN);
        $first = $segmentOrigin[0];

        $this->assertEquals($first->id, 1);
        $this->assertEquals($first->name, 'MT');
    }

    /**
     * @test
     */
    public function get_catTool()
    {
        $catTool = BasicAttributes::get(BasicAttributes::CAT_TOOL);
        $first = $catTool[0];

        $this->assertEquals($first->id, 1);
        $this->assertEquals($first->name, '<none>');
    }

    /**
     * @test
     */
    public function get_industry()
    {
        $industry = BasicAttributes::get(BasicAttributes::INDUSTRY);
        $first = $industry[0];

        $this->assertEquals($first->id, 1);
        $this->assertEquals($first->name, 'Aerospace/Aviation');
    }

    /**
     * @test
     */
    public function get_errorCategory()
    {
        $catTool = BasicAttributes::get(BasicAttributes::ERROR_CATEGORY);
        $first = $catTool[0];

        $this->assertEquals($first->id, 1);
        $this->assertEquals($first->name, 'Accuracy');
    }

    /**
     * @test
     */
    public function get_qualitylevel()
    {
        $qualitylevel = BasicAttributes::get(BasicAttributes::QUALITY_LEVEL);
        $first = $qualitylevel[0];

        $this->assertEquals($first->id, 1);
        $this->assertEquals($first->name, 'Good Enough');
    }

    /**
     * @test
     */
    public function get_fromId()
    {
        $segmentOrigin = BasicAttributes::getFromId(BasicAttributes::SEGMENT_ORIGIN, 1);

        $this->assertEquals($segmentOrigin->name, 'MT');
        $this->assertEquals($segmentOrigin->id, 1);
    }

    /**
     * @test
     */
    public function get_fromName()
    {
        $segmentOrigin = BasicAttributes::getFromName(BasicAttributes::SEGMENT_ORIGIN, 'MT');

        $this->assertEquals($segmentOrigin->name, 'MT');
        $this->assertEquals($segmentOrigin->id, 1);
    }

    /**
     * @test
     * @throws CacheException
     */
    public function refresh()
    {
        $dataFile = __DIR__.'/../../src/Cache/data/attributes.json';

        if (file_exists($dataFile)) {
            unlink($dataFile);
            sleep(4);
        }

        BasicAttributes::refresh($this->client);

        $this->assertTrue(file_exists($dataFile));
    }

    /**
     * @test
     * @throws CacheException
     */
    public function refreshWithDifferentDataPath()
    {
        $dataFile = __DIR__.'/../../cache/attributes.json';

        if (file_exists($dataFile)) {
            unlink($dataFile);
            sleep(4);
        }

        BasicAttributes::setDataFile($dataFile);
        BasicAttributes::refresh($this->client);
        $this->assertTrue(file_exists($dataFile));

        try {
            new MasterProject('test', 'en-US', 1, 2, 3, 5);
        } catch (\DomainException $e) {
            $this->assertEquals($dataFile, BasicAttributes::getDataFile());
            $this->assertEquals('5 is not a valid value. [Allowed: 1,2]', $e->getMessage());
        }
    }
}
