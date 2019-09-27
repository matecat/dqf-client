<?php

namespace Matecat\Dqf\Utils;

class ParamsValidator
{
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

            // check for param types
            if (isset($params[ $key ]) and gettype($params[ $key ]) !== $rule[ 'type' ]) {
                $errors[] = self::createWrongTypeParamsErrorText($key, $rule[ 'type' ]);
            }

            // values
            if (isset($rule[ 'values' ]) and isset($params[ $key ])) {
                $values = explode('|', $rule[ 'values' ]);
                if (false === in_array($params[ $key ], $values)) {
                    $errors[] = self::createNotAllowedParamsWithPossibleValuesErrorText($key, $rule[ 'values' ]);
                }
            }


            // execute callbacks
            if (isset($rule[ 'callback' ]) and isset($params[ $key ])) {
                if (false === call_user_func($rule[ 'callback' ], $params[ $key ], $params)) {
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

    /**
     * @param string $key
     * @param string $values
     *
     * @return string
     */
    protected static function createNotAllowedParamsWithPossibleValuesErrorText($key, $values)
    {
        return "'{$key}' param is not allowed (only {$values} are permitted)";
    }
}
