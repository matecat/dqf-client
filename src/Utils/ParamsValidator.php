<?php

namespace Matecat\Dqf\Utils;

class ParamsValidator
{
    const DATA_TYPE_BOOLEAN  = 'boolean';
    const DATA_TYPE_INTEGER  = 'integer';
    const DATA_TYPE_DOUBLE   = 'double';
    const DATA_TYPE_STRING   = 'string';
    const DATA_TYPE_ARRAY    = 'array';
    const DATA_TYPE_OBJECT   = 'object';
    const DATA_TYPE_RESOURCE = 'resource';
    const DATA_TYPE_NULL     = 'NULL';

    /**
     * @param array $params
     * @param array $rules
     *
     * @return array
     */
    public static function validate(array $params, array $rules)
    {
        $errors = [];

        foreach ($rules as $key => $rule) {

            // check for required params
            if ($rule[ 'required' ] and false === isset($params[ $key ])) {
                $errors[] = self::createMissingParamsErrorText($key);
            }

            // check for wrong types
            if (isset($params[ $key ]) and gettype($params[ $key ]) !== $rule[ 'type' ]) {
                $errors[] = self::createWrongTypeParamsErrorText($key, $rule[ 'type' ]);
            }

            // execute callbacks
            if (isset($rule[ 'callback' ]) and isset($params[ $key ])) {
                if(false === call_user_func($rule[ 'callback' ], $params[ $key ], $params)){
                    $errors[] = self::createCallbackErrorText($key);
                }
            }
        }

        // check for not allowed params
        foreach (array_keys($params) as $key) {
            if (false === isset($rules[ $key ])) {
                $errors[] = self::createNotAllowedParamsErrorText($key);
            }
        }

        return $errors;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private static function createMissingParamsErrorText($key)
    {
        return "'{$key}' param is missing";
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private static function createNotAllowedParamsErrorText($key)
    {
        return "'{$key}' param is not allowed";
    }

    /**
     * @param string $key
     * @param string $type
     *
     * @return string
     */
    private static function createWrongTypeParamsErrorText($key, $type)
    {
        return "'{$key}' param is a wrong type ({$type} is required)";
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private static function createCallbackErrorText($key)
    {
        return "'{$key}' param did not pass callback validation";
    }
}
