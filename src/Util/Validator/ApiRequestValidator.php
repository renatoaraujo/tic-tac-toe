<?php

namespace TicTacToe\Util\Validator;

use TicTacToe\Exception\InvalidRequestException;
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
     * @throws InvalidRequestException
     */
    public function isValid(?string $content): bool
    {
        $body = json_decode($content, true);

        if (empty($content)) {
            throw new InvalidRequestException("Invalid request. Empty body.");
        }

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
     * @throws InvalidRequestException
     */
    public function isValidRequestBodyContent(array $body): bool
    {
        if (!array_key_exists('playerUnit', $body)) {
            throw new InvalidRequestException("You must provide the playerUnit.");
        }

        if (!array_key_exists('boardState', $body)) {
            throw new InvalidRequestException("You must provide the boardState.");
        }

        return true;
    }

    /**
     * @param array $boardState
     *
     * @return bool
     * @throws InvalidRequestException
     */
    public function hasValidBoardState(array $boardState): bool
    {
        if (empty($boardState) || count($boardState) !== 3) {
            throw new InvalidRequestException("The boardState is invalid! A valid boardState has 3 lines.");
        }

        foreach ($boardState as $lineValues) {
            if (count($lineValues) !== 3) {
                throw new InvalidRequestException("The boardState is invalid! A valid boardState line has 3 moves.");
            }

            $invalidValues = array_filter($lineValues, function($move) {
                return ($move != GameUnit::O_UNIT && $move != GameUnit::X_UNIT && !empty($move));
            });

            if ($invalidValues) {
                throw new InvalidRequestException("The boardState must contain only 'X', 'O' or empty moves.");
            }
        }

        return true;
    }

    /**
     * @param string $playerUnit
     *
     * @return bool
     * @throws InvalidRequestException
     */
    public function hasValidPlayerUnit(string $playerUnit): bool
    {
        if ($playerUnit != GameUnit::O_UNIT && $playerUnit != GameUnit::X_UNIT) {
            throw new InvalidRequestException("The playerUnit is invalid! Accept only 'X' or 'O' options.");
        }

        return true;
    }
}
