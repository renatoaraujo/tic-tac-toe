<?php

namespace TicTacToe\Util\Validator;

/**
 * Class ApiRequestValidator
 * @package TicTacToe\Util\Validator
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class ApiRequestValidator
{
    /**
     * @param string $requestContent
     *
     * @return bool
     */
    public static function isValidRequestBodyContent(string $requestContent): bool
    {
        $arrayContent = json_decode($requestContent, true);

        if (!array_key_exists('playerUnit', $arrayContent)) {
            return false;
        }

        if (!array_key_exists('boardState', $arrayContent)) {
            return false;
        }

        return true;
    }
}
