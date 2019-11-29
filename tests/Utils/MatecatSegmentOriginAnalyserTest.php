<?php

namespace Matecat\Dqf\Tests\SessionProvider;

use Matecat\Dqf\Utils\Analysers\MatecatSegmentOriginAnalyser;

class MatecatSegmentOriginAnalyserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function ICE_should_return_TM_and_sm_100()
    {
        $row = [
             'autopropagated_from' => null,
             'suggestions_array' => '[{"id":"474271016","raw_segment":"[MateCat] Introduzione DIC","segment":"[MateCat] Introduzione DIC","translation":"[Mat\u00e9Cat] Introduction DIC","target_note":"","raw_translation":"[Mat\u00e9Cat] Introduction DIC","quality":"74","reference":null,"usage_count":1,"subject":"All","created_by":"MateCat","last_updated_by":"MateCat","create_date":"2019-11-07 12:20:26","last_update_date":"2019-11-07","match":"100%","prop":[],"memory_key":"","ICE":true,"tm_properties":[{"type":"x-project_id","value":"2507911"},{"type":"x-project_name","value":"_MateCat_Introduzione_DIC.docx"},{"type":"x-job_id","value":"2465319"}],"source_note":""}]',
             'match_type' => 'ICE',
             'suggestion_match' => '100',
             'suggestion_position' => 1,
             'suggestion' => '[MatéCat] Introduction DIC',
             'translation' => '[MatéCat] Introduction DIC',
        ];

        $this->assertAnalyseIsCorresponding($row, 'TM', 100);
    }

    /**
     * @test
     */
    public function percentage_should_return_TM()
    {
        $row = [
            'autopropagated_from' => null,
            'suggestions_array' => '[{"id":"414876725","raw_segment":"- 00...","segment":"- 00...","translation":"- 00...","target_note":"","raw_translation":"- 00...","quality":"0","reference":null,"usage_count":1,"subject":"All","created_by":"Public_Corpora","last_updated_by":"Public_Corpora","create_date":"2016-10-28 09:29:13","last_update_date":"2016-10-28","match":"94%","prop":[],"memory_key":"","ICE":false,"tm_properties":[{"type":"x-tuid","value":null},{"type":"x-creation-date","value":"2016-10-28 09:29:13"},{"type":"x-change-date","value":"2016-10-28 09:29:13"}],"source_note":""},{"id":0,"raw_segment":"00","segment":"00","translation":"00","target_note":"","raw_translation":"00","quality":70,"reference":"Machine Translation provided by Google, Microsoft, Worldlingo or MyMemory customized engine.","usage_count":1,"subject":false,"created_by":"MT!","last_updated_by":"MT!","create_date":"2019-08-19 10:53:08","last_update_date":"2019-08-19","match":"85%","prop":[],"memory_key":"","ICE":false,"tm_properties":null,"source_note":""},{"id":"414954617","raw_segment":"10:00","segment":"10:00","translation":"10h","target_note":"","raw_translation":"10h","quality":"74","reference":null,"usage_count":1,"subject":"All","created_by":"MateCat","last_updated_by":"MateCat","create_date":"2018-08-24 04:33:05","last_update_date":"2018-08-24","match":"80%","prop":[],"memory_key":"","ICE":false,"tm_properties":[{"type":"x-project_id","value":"1406500"},{"type":"x-project_name","value":"21021123"},{"type":"x-job_id","value":"1490264"}],"source_note":""}]',
            'match_type' => '85%-94%',
            'suggestion_match' => '94',
            'suggestion_position' => null,
            'suggestion' => '- 00...',
            'translation' => '- 00...',
        ];

        $this->assertAnalyseIsCorresponding($row, 'TM', 94);
    }

    /**
     * @test
     */
    public function sdfdsfdsfdsfdsfdfds()
    {
        $row = [
            'autopropagated_from' => null,
            'suggestions_array' => '[{"id":0,"raw_segment":"200, rue de Paris","segment":"200, rue de Paris","translation":"200, rue de Paris","target_note":"","raw_translation":"200, rue de Paris","quality":70,"reference":"Machine Translation provided by Google, Microsoft, Worldlingo or MyMemory customized engine.","usage_count":1,"subject":false,"created_by":"MT!","last_updated_by":"MT!","create_date":"2019-08-19 10:53:07","last_update_date":"2019-08-19","match":"85%","prop":[],"memory_key":"","ICE":false,"tm_properties":null,"source_note":""},{"id":"414488218","raw_segment":"Dua t\u00eb raportoj nj\u00eb vjedhje n\u00eb \"Hotel de Paris\".","segment":"Dua t\u00eb raportoj nj\u00eb vjedhje n\u00eb \"Hotel de Paris\".","translation":"Je voudrais signaler un vol \u00e0 l\'H\u00f4tel de Paris.","target_note":"","raw_translation":"Je voudrais signaler un vol \u00e0 l\'H\u00f4tel de Paris.","quality":"0","reference":null,"usage_count":1,"subject":"All","created_by":"Public_Corpora","last_updated_by":"Public_Corpora","create_date":"2016-10-28 09:29:13","last_update_date":"2016-10-28","match":"26%","prop":[],"memory_key":"","ICE":false,"tm_properties":[{"type":"x-tuid","value":null},{"type":"x-creation-date","value":"2016-10-28 09:29:13"},{"type":"x-change-date","value":"2016-10-28 09:29:13"}],"source_note":""}]',
            'match_type' => 'MT',
            'suggestion_match' => '85',
            'suggestion_position' => null,
            'suggestion' => '200, rue de Paris',
            'translation' => '200, rue de Paris',
        ];

        $this->assertAnalyseIsCorresponding($row, 'MT', null);
    }

    /**
     * @test
     */
    public function REPETITIONS_should_return_TM()
    {
        $row = [
            'autopropagated_from' => null,
            'suggestions_array' => '[{"id":0,"raw_segment":"Mandat-gestion","segment":"Mandat-gestion","translation":"Mandat- gestion","target_note":"","raw_translation":"Mandat- gestion","quality":70,"reference":"Machine Translation provided by Google, Microsoft, Worldlingo or MyMemory customized engine.","usage_count":1,"subject":false,"created_by":"MT!","last_updated_by":"MT!","create_date":"2019-08-19 11:01:38","last_update_date":"2019-08-19","match":"85%","prop":[],"memory_key":"","ICE":false,"tm_properties":null,"source_note":""},{"id":"414520865","raw_segment":"Ke mandat?","segment":"Ke mandat?","translation":"Vous avez un mandat ?","target_note":"","raw_translation":"Vous avez un mandat ?","quality":"0","reference":null,"usage_count":1,"subject":"All","created_by":"Public_Corpora","last_updated_by":"Public_Corpora","create_date":"2016-10-28 09:29:13","last_update_date":"2016-10-28","match":"52%","prop":[],"memory_key":"","ICE":false,"tm_properties":[{"type":"x-tuid","value":null},{"type":"x-creation-date","value":"2016-10-28 09:29:13"},{"type":"x-change-date","value":"2016-10-28 09:29:13"}],"source_note":""},{"id":"414805250","raw_segment":"Kam nj\u00eb mandat...","segment":"Kam nj\u00eb mandat...","translation":"J\'ai un mandat...","target_note":"","raw_translation":"J\'ai un mandat...","quality":"0","reference":null,"usage_count":1,"subject":"All","created_by":"Public_Corpora","last_updated_by":"Public_Corpora","create_date":"2016-10-28 09:29:13","last_update_date":"2016-10-28","match":"46%","prop":[],"memory_key":"","ICE":false,"tm_properties":[{"type":"x-tuid","value":null},{"type":"x-creation-date","value":"2016-10-28 09:29:13"},{"type":"x-change-date","value":"2016-10-28 09:29:13"}],"source_note":""}]',
            'match_type' => 'REPETITIONS',
            'suggestion_match' => '85',
            'suggestion_position' => null,
            'suggestion' => 'Mandat- gestion',
            'translation' => 'Mandat- gestion',
        ];

        $this->assertAnalyseIsCorresponding($row, 'TM', 100);
    }

    /**
     * @test
     */
    public function an_empty_suggestions_array_should_return_HT()
    {
        $row = [
                'autopropagated_from' => null,
                'suggestions_array' => null,
                'match_type' => 'NO_MATCH',
                'suggestion_match' => null,
                'suggestion_position' => 0,
                'suggestion' => null,
                'translation' => 'Bricostore Hrvatska d.o.o.',
        ];

        $this->assertAnalyseIsCorresponding($row, 'HT', null);
    }

    /**
     * @test
     */
    public function the_chosen_suggestion_is_not_MT()
    {
        $row = [
                'autopropagated_from' => null,
                'suggestions_array' => '[{"id":"0","segment":"&lt;g id=\"177\"&gt;LEARNS&lt;\/g&gt;\t&lt;g id=\"178\"&gt;JAVA&lt;\/g&gt;","translation":"&lt;g id=\"177\"&gt; IMPARA &lt;\/g&gt;&lt;g id=\"178\"&gt; JAVA &lt;\/g&gt;","raw_translation":"<g id=\"177\"> IMPARA <\/g><g id=\"178\"> JAVA <\/g>","quality":"70","reference":"Machine Translation provided by Google, Microsoft, Worldlingo or MyMemory customized engine.","usage_count":1,"subject":"All","created_by":"MT!","last_updated_by":"MT!","create_date":"2013-03-15","last_update_date":"2013-03-15","match":"85%"},{"id":"434501309","segment":"&lt;g id=\"111\"&gt;SECTION&lt;\/g&gt;\t&lt;g id=\"112\"&gt;PAGE&lt;\/g&gt;","translation":"&lt;g id=\"111\"&gt; SEZIONE &lt;\/g&gt;&lt;g id=\"112\"&gt; PAGINA &lt;\/g&gt;","raw_translation":"<g id=\"111\"> SEZIONE <\/g><g id=\"112\"> PAGINA <\/g>","quality":"74","reference":"","usage_count":1,"subject":"All","created_by":"anonymous","last_updated_by":"anonymous","create_date":"2013-02-25 15:39:32","last_update_date":"2013-02-25","match":"21%"},{"id":"431027350","segment":"G","translation":"F","raw_translation":"F","quality":"","reference":"http:\/\/www.emea.europa.eu\/|@|http:\/\/www.emea.europa.eu\/","usage_count":1,"subject":"Pharmaceuticals","created_by":"MyMemoryLoader","last_updated_by":"MyMemoryLoader","create_date":"2012-04-12 18:42:02","last_update_date":"2012-04-12","match":"20%"}]',
                'match_type' => 'MT',
                'suggestion_match' => '85',
                'suggestion_position' => 1,
                'suggestion' => '<g id="177"> IMPARA </g><g id="178"> JAVA </g>',
                'translation' => '<g id="177">IMPARA</g><g id="178">JAVA</g>',
        ];

        $this->assertAnalyseIsCorresponding($row, 'TM', 21);
    }

    /**
     * @test
     */
    public function MT_with_different_suggestion_and_translation()
    {
        $row = [
                'autopropagated_from' => null,
                'suggestions_array' => '[{"id":"0","segment":"Your home is your sanctuary from the world \u2014 or it should be.","translation":"Dit hjem er dit fristed fra verden - eller det skal v\u00e6re.","raw_translation":"Dit hjem er dit fristed fra verden - eller det skal v\u00e6re.","quality":"70","reference":"Machine Translation provided by Google, Microsoft, Worldlingo or MyMemory customized engine.","usage_count":1,"subject":"All","created_by":"MT!","last_updated_by":"MT!","create_date":"2013-02-25","last_update_date":"2013-02-25","match":"85%"},{"id":"413721806","segment":"where is your photo","translation":"hvor er dit foto","raw_translation":"hvor er dit foto","quality":"74","reference":"","usage_count":1,"subject":"General","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2010-06-18 20:09:53","last_update_date":"2010-06-18","match":"17%"},{"id":"424290266","segment":"\ufeffIt is based on the","translation":"farve","raw_translation":"farve","quality":"74","reference":"","usage_count":1,"subject":"General","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2011-10-04 20:23:40","last_update_date":"2011-10-04","match":"15%"},{"id":"427707852","segment":"\ufeffThis is the best part of verdensl\u00e5","translation":"elsker dig","raw_translation":"elsker dig","quality":"74","reference":"","usage_count":1,"subject":"General","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2012-03-15 10:09:30","last_update_date":"2012-03-15","match":"14%"},{"id":"432840673","segment":"Printed from the Civil Registration System","translation":"Udskrift fra Det Centrale Person Register","raw_translation":"Udskrift fra Det Centrale Person Register","quality":"74","reference":"","usage_count":1,"subject":"All","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2012-12-19 16:49:09","last_update_date":"2012-12-19","match":"13%"},{"id":"248656188","segment":"The amounts receivable can be collected as they fall due","translation":"Fordringerne kan inddrives efterh\u00e5nden som de forfalder","raw_translation":"Fordringerne kan inddrives efterh\u00e5nden som de forfalder","quality":"74","reference":"","usage_count":1,"subject":"Legal_and_Notarial","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2009-11-23 00:00:51","last_update_date":"2009-11-23","match":"12%"},{"id":"424290514","segment":"your","translation":"\ufefflips","raw_translation":"\ufefflips","quality":"74","reference":"","usage_count":1,"subject":"General","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2011-12-21 10:31:53","last_update_date":"2011-12-21","match":"11%"},{"id":"427707572","segment":"This compromise is welcome but it is not enough;","translation":"Det er et udm\u00e6rket kompromis, som vi gl\u00e6der os over, men det er ikke tilstr\u00e6kkeligt.","raw_translation":"Det er et udm\u00e6rket kompromis, som vi gl\u00e6der os over, men det er ikke tilstr\u00e6kkeligt.","quality":"74","reference":"","usage_count":1,"subject":"General","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2012-03-01 12:56:17","last_update_date":"2012-03-01","match":"10%"}]',
                'match_type' => 'MT',
                'suggestion_match' => '85',
                'suggestion_position' => null,
                'suggestion' => 'Dit hjem er dit fristed fra verden - eller det skal være.',
                'translation' => 'Dit hjem er dit fristed fra verden - eller det burde det være.',
        ];

        $this->assertAnalyseIsCorresponding($row, 'MT', null);
    }

    /**
     * @test
     */
    public function PUBLIC_100_should_return_TM()
    {
        $row = [
                'autopropagated_from' => null,
                'suggestions_array' => '[{"id":"750522609","raw_segment":"00:01:12.840,0:01:16.459\rSeine-Saint-Denis habitat, \u00e0 vos c\u00f4t\u00e9s, pour am\u00e9liorer la qualit\u00e9 de service.","segment":"00:01:12.840,0:01:16.459\rSeine-Saint-Denis habitat, \u00e0 vos c\u00f4t\u00e9s, pour am\u00e9liorer la qualit\u00e9 de service.","translation":"00:01:12.840,0:01:16.459\nSeine-Saint-Denis habitat is by your side to improve the quality of service.","target_note":"","raw_translation":"00:01:12.840,0:01:16.459\nSeine-Saint-Denis habitat is by your side to improve the quality of service.","quality":"74","reference":null,"usage_count":3,"subject":"All","created_by":"MateCat","last_updated_by":"MateCat","create_date":"2019-11-13 13:44:39","last_update_date":"2019-11-13","match":"100%","prop":[],"memory_key":"","ICE":false,"tm_properties":[{"type":"x-project_id","value":"2521150"},{"type":"x-project_name","value":"26629965"},{"type":"x-job_id","value":"2479111"}],"source_note":""}]',
                'match_type' => '100%_PUBLIC',
                'suggestion_match' => '100',
                'suggestion_position' => null,
                'suggestion' => '00:01:02.289,0:01:07.889
The possession of dangerous first-class dogs and non-domestic animals is prohibited in the housing.',
                'translation' => '00:01:12.840,0:01:16.459
Seine-Saint-Denis habitat is by your side to improve the quality of service.',
        ];

        $this->assertAnalyseIsCorresponding($row, 'TM', 100);
    }

    /**
     * @param array $row
     * @param string $segment_origin
     * @param string $suggestion_match
     */
    private function assertAnalyseIsCorresponding(array $row, $segment_origin, $suggestion_match = null)
    {
        $analyse = MatecatSegmentOriginAnalyser::analyse($row);
        $expected = [
            'segment_origin' => $segment_origin,
            'suggestion_match' => $suggestion_match,
        ];

        $this->assertEquals($analyse, $expected);
    }
}
