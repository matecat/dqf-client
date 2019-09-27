<?php

namespace Matecat\Dqf\Utils;

class ParamsValidator {
    /**
     * @param array $params
     * @param array $rules
     *
     * @return array
     */
    public static function validate( array $params, array $rules ) {
        $errors = [];

        foreach ( $rules as $key => $rule ) {

            // check for required params
            if ( $rule[ 'required' ] and false === isset( $params[ $key ] ) ) {
                $errors[] = self::createMissingParamsErrorText( $key );
            }

            // check required_if
            if ( isset( $rule[ 'required_if' ] ) and self::evalRequiredIf( $rule[ 'required_if' ], $params ) and false === isset( $params[ $key ] )) {
                $errors[] = self::createConditionalMissingParamsErrorText( $key, $rule[ 'required_if' ] );
            }

            // check for param types
            if ( isset( $params[ $key ] ) and gettype( $params[ $key ] ) !== $rule[ 'type' ] ) {
                $errors[] = self::createWrongTypeParamsErrorText( $key, $rule[ 'type' ] );
            }

            // values
            if ( isset( $rule[ 'values' ] ) and isset( $params[ $key ] ) ) {
                $values = explode( '|', $rule[ 'values' ] );
                if ( false === in_array( $params[ $key ], $values ) ) {
                    $errors[] = self::createNotAllowedParamsWithPossibleValuesErrorText( $key, $rule[ 'values' ] );
                }
            }

            // execute callbacks
            if ( isset( $rule[ 'callback' ] ) and isset( $params[ $key ] ) ) {
                if ( false === call_user_func( $rule[ 'callback' ], $params[ $key ], $params ) ) {
                    $errors[] = self::createCallbackErrorText( $key );
                }
            }
        }

        // check for not allowed params
        foreach ( array_keys( $params ) as $key ) {
            if ( false === isset( $rules[ $key ] ) ) {
                $errors[] = self::createNotAllowedParamsErrorText( $key );
            }
        }

        return $errors;
    }

    /**
     * @param array $requiredIf
     * @param array $params
     *
     * @return bool
     */
    private static function evalRequiredIf( array $requiredIf, array $params ) {
        $paramKey = $requiredIf[ 0 ];
        $operator = $requiredIf[ 1 ];
        $values   = explode('|', $requiredIf[ 2 ]);

        $matches = 0;

        foreach ($values as $value){
            $condition = '';
            $condition .= ( false === is_int( $params[ $paramKey ] ) ) ? '\'' : '';
            $condition .= $params[ $paramKey ];
            $condition .= ( false === is_int( $params[ $paramKey ] ) ) ? '\'' : '';
            $condition .= $operator;
            $condition .= ( false === is_int( $value ) ) ? '\'' : '';
            $condition .= $value;
            $condition .= ( false === is_int( $value ) ) ? '\'' : '';

            $eval = eval( 'return ' . $condition . ';' );

            if ($eval) {
                $matches++;
            }
        }

        return $matches > 0;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private static function createMissingParamsErrorText( $key ) {
        return "'{$key}' param is missing";
    }

    /**
     * @param string $key
     * @param array  $requiredIf
     *
     * @return string
     */
    private static function createConditionalMissingParamsErrorText( $key, array $requiredIf ) {
        return "'{$key}' param is missing (conditional statement: {$requiredIf[0]} {$requiredIf[1]} {$requiredIf[2]})";
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private static function createNotAllowedParamsErrorText( $key ) {
        return "'{$key}' param is not allowed";
    }

    /**
     * @param string $key
     * @param string $type
     *
     * @return string
     */
    private static function createWrongTypeParamsErrorText( $key, $type ) {
        return "'{$key}' param is a wrong type ({$type} is required)";
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private static function createCallbackErrorText( $key ) {
        return "'{$key}' param did not pass callback validation";
    }

    /**
     * @param string $key
     * @param string $values
     *
     * @return string
     */
    protected static function createNotAllowedParamsWithPossibleValuesErrorText( $key, $values ) {
        return "'{$key}' param is not allowed (only {$values} are permitted)";
    }
}
