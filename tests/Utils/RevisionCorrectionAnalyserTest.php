<?php

namespace Matecat\Dqf\Tests\SessionProvider;

use Matecat\Dqf\Utils\RevisionCorrectionAnalyser;

class RevisionCorrectionAnalyserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_analyse_the_differences_between_two_strings_and_returns_in_as_a_structured_array() {
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
        ];

        foreach ( $data as $item ) {
            $analysed = RevisionCorrectionAnalyser::analyse( $item[ 'old_string' ], $item[ 'new_string' ] );

            foreach ($analysed as $key => $value){
                $this->assertArrayHasKey($key, $item[ 'expected' ]);
                $this->assertEquals($item[ 'expected' ][$key], $value);
            }
        }
    }
}