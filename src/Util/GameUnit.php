<?php

namespace TicTacToe\Util;

/**
 * Class GameUnit
 * @package TicTacToe\Util
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class GameUnit
{
    /**
     * @var string
     */
    public const X_UNIT = 'X';

    /**
     * @var string
     */
    public const O_UNIT = 'O';

    /**
     * @param string $unit
     *
     * @return string
     */
    public static function getInverseUnit(string $unit): string
    {
        return $unit === self::X_UNIT ? self::O_UNIT : self::X_UNIT;
    }
}
