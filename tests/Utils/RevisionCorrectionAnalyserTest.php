<?php

namespace Matecat\Dqf\Tests\SessionProvider;

use Matecat\Dqf\Utils\RevisionCorrectionAnalyser;

class RevisionCorrectionAnalyserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_analyse_the_differences_between_two_strings_and_returns_in_as_a_structured_array()
    {
        $data = [
            'test_1' => [
                'old_string' => 'Test Segment',
                'new_string' => 'Some in Test and also added.',
                'expected'   => [
                    'Some in'         => 'added',
                    'Test'            => 'unchanged',
                    'Segment'         => 'deleted',
                    'and also added.' => 'added',
                ],
            ],
            'test_2' => [
                'old_string' => 'Test Segment',
                'new_string' => 'Test Segment',
                'expected' => [
                    'Test Segment' => 'unchanged',
                ],
            ],
            'test_3' => [
                'old_string' => 'This is just a Test Segment',
                'new_string' => 'Test Segment',
                'expected' => [
                    'This is just a' => 'deleted',
                    'Test Segment' => 'unchanged',
                ],
            ],
            'test_4' => [
                'old_string' => 'This is just a Test Segment',
                'new_string' => 'Wow, This is just a brand new Test Segment',
                'expected' => [
                    'This is just a' => 'unchanged',
                    'brand new' => 'added',
                    'Wow,' => 'added',
                    'brand' => 'added',
                    'Test Segment' => 'unchanged',
                ],
            ],
            'test_5' => [
                    'old_string' => 'This is just a Test Segment',
                    'new_string' => 'This is just',
                    'expected' => [
                            'This is just' => 'unchanged',
                            'a Test Segment' => 'deleted',
                    ],
            ],
            'test_6' => [
                    'old_string' => '. سعدت بلقائك.',
                    'new_string' => ". سعدت",
                    'expected' => [
                            'بلقائك.' => 'deleted',
                            '. سعدت' => 'unchanged',
                    ],
            ],
            'test_7' => [
                    'old_string' => '夏目漱石 私の個人主義',
                    'new_string' => '夏目漱石 自分で指定（A4で1-2枚程度)',
                    'expected' => [
                            '夏目漱石' => 'unchanged',
                            '私の個人主義' => 'deleted',
                            '自分で指定（A4で1-2枚程度)' => 'added',
                    ],
            ],
        ];

        foreach ($data as $item) {
            $analysed = RevisionCorrectionAnalyser::analyse($item[ 'old_string' ], $item[ 'new_string' ]);

            foreach ($analysed as $key => $value) {
                $this->assertArrayHasKey($key, $item[ 'expected' ]);
                $this->assertEquals($item[ 'expected' ][$key], $value);
            }
        }
    }
}
