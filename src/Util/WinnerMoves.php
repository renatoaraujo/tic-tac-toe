<?php

namespace TicTacToe\Util;

/**
 * Class WinnerMoves
 * @package TicTacToe\Util
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class WinnerMoves
{
    /**
     * @return array
     */
    public static function getWinnerMoves(): array
    {
        $winnerMoves = [];
        $winnerMoves[] = [
            [2, 0],
            [1, 1],
            [0, 2],
        ];

        $winnerMoves[] = [
            [0, 0],
            [1, 1],
            [2, 2],
        ];

        for ($x = 0; $x < 3; $x++) {
            $winnerMoves[] = [
                [$x, 0],
                [$x, 1],
                [$x, 2],
            ];
            $winnerMoves[] = [
                [0, $x],
                [1, $x],
                [2, $x],
            ];
        }

        return $winnerMoves;
    }
}
