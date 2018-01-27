<?php

namespace TicTacToe\Util\Validator;

use TicTacToe\Util\GameUnit;

/**
 * Class ApiRequestValidator
 * @package TicTacToe\Util\Validator
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class ApiRequestValidator
{
    /**
     * @param string $content
     *
     * @return bool
     */
    public static function isValid(string $content): bool
    {
        $body = json_decode($content, true);

        return (
            self::isValidRequestBodyContent($body) &&
            self::hasValidBoardState($body['boardState']) &&
            self::hasValidPlayerUnit($body['playerUnit'])
        );
    }

    /**
     * @param array $body
     *
     * @return bool
     */
    public static function isValidRequestBodyContent(array $body): bool
    {
        if (!array_key_exists('playerUnit', $body)) {
            return false;
        }

        if (!array_key_exists('boardState', $body)) {
            return false;
        }

        return true;
    }

    /**
     * @param array $boardState
     *
     * @return bool
     */
    public static function hasValidBoardState(array $boardState): bool
    {
        $isValid = true;

        if (empty($boardState) || count($boardState) !== 3) {
            $isValid = false;
        }

        foreach ($boardState as $lineValues) {
            if (count($lineValues) !== 3) {
                $isValid = false;
            }
        }

        return $isValid;
    }

    /**
     * @param string $playerUnit
     *
     * @return bool
     */
    public static function hasValidPlayerUnit(string $playerUnit): bool
    {
        return ($playerUnit === GameUnit::O_UNIT || $playerUnit === GameUnit::X_UNIT);
    }
}
