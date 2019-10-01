<?php

namespace Matecat\Dqf\Tests;

use Matecat\Dqf\Constants;
use Matecat\Dqf\Utils\ParamsValidator;

class ParamsValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function throw_exception_if_required_params_are_missing()
    {
        $rules = [
                'email'    => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'password' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'isDummy'  => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_BOOLEAN,
                ],
        ];

        $params = [
                'password' => 'rerewrewrwe',
        ];

        $validate = ParamsValidator::validate($params, $rules);
        $this->assertEquals([ '\'email\' param is missing' ], $validate);
    }

    /**
     * @test
     */
    public function throw_exception_if_wrong_type_params_are_passed()
    {
        $rules = [
                'email'    => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'password' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'isDummy'  => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_BOOLEAN,
                ],
        ];

        $params = [
                'email'    => 23,
                'password' => 'rerewrewrwe',
                'isDummy'  => false,
        ];

        $validate = ParamsValidator::validate($params, $rules);
        $this->assertEquals([ '\'email\' param is a wrong type (string is required)' ], $validate);
    }

    /**
     * @test
     */
    public function throw_exception_if_not_allowed_params_are_passed()
    {
        $rules = [
                'email'    => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'password' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'isDummy'  => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_BOOLEAN,
                ],
        ];

        $params = [
                'email'    => 23,
                'password' => 'rerewrewrwe',
                'isDummy'  => 'false',
                'foo'      => 'bar',
        ];

        $validate = ParamsValidator::validate($params, $rules);

        $this->assertEquals([
                '\'email\' param is a wrong type (string is required)',
                '\'isDummy\' param is a wrong type (boolean is required)',
                '\'foo\' param is not allowed'
        ], $validate);
    }

    /**
     * @test
     */
    public function throw_exception_with_possibile_values()
    {
        $rules = [
                'review_type' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                        'values'   => 'correction|error_typology|combined'
                ],
        ];

        $params = [
                'review_type' => 'bar',
        ];

        $validate = ParamsValidator::validate($params, $rules);

        $this->assertEquals([ '\'review_type\' param is not allowed (only correction|error_typology|combined are permitted)' ], $validate);
    }

    /**
     * @test
     */
    public function throw_exception_with_required_if_validation()
    {
        $rules = [
                'passFailThreshold' => [
                        'required'    => false,
                        'type'        => Constants::DATA_TYPE_DOUBLE,
                        'required_if' => [ 'reviewType', Constants::LOGICAL_OPERATOR_EQUALS, 'combined|error_typology' ]
                ],
                'reviewType'        => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
        ];

        $params = [
                'reviewType' => 'combined',
        ];

        $validate = ParamsValidator::validate($params, $rules);
        $this->assertCount(1, $validate);
        $this->assertEquals("'passFailThreshold' param is missing (conditional statement: reviewType === combined|error_typology)", $validate[0]);

        $params = [
                'reviewType' => 'error_typology',
        ];

        $validate = ParamsValidator::validate($params, $rules);
        $this->assertCount(1, $validate);
        $this->assertEquals("'passFailThreshold' param is missing (conditional statement: reviewType === combined|error_typology)", $validate[0]);

        $params = [
                'reviewType'        => 'combined',
                'passFailThreshold' => 1.0,
        ];

        $validate = ParamsValidator::validate($params, $rules);
        $this->assertCount(0, $validate);

        $params = [
                'reviewType'        => 'error_typology',
                'passFailThreshold' => 1.0,
        ];

        $validate = ParamsValidator::validate($params, $rules);
        $this->assertCount(0, $validate);
    }

    /**
     * @test
     */
    public function throw_exception_with_callback_validation()
    {
        $rules = [
                'max' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                        'callback' => function ($value, $params) {
                            return $value >= $params[ 'min' ];
                        }
                ],
                'min' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                        'callback' => function ($value, $params) {
                            return $value <= $params[ 'max' ];
                        }
                ],
        ];

        $params = [
                'max' => 23,
                'min' => 112
        ];

        $validate = ParamsValidator::validate($params, $rules);
        $this->assertCount(2, $validate);
    }

    /**
     * @test
     */
    public function validation_passes()
    {
        $rules = [
                'email'    => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'password' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'isDummy'  => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_BOOLEAN,
                ],
        ];

        $params = [
                'email'    => 'mauro@translated.net',
                'password' => 'rerewrewrwe',
        ];

        $this->assertCount(0, ParamsValidator::validate($params, $rules));

        $params = [
                'email'    => 'mauro@translated.net',
                'password' => 'rerewrewrwe',
                'isDummy'  => true,
        ];

        $this->assertCount(0, ParamsValidator::validate($params, $rules));
    }
}
