<?php

namespace TicTacToe\Util;

/**
 * Class GameStatus
 * @package TicTacToe\Util
 */
class GameStatus
{
    /**
     * @var bool
     */
    private $isTied = false;

    /**
     * @var array
     */
    private $winner = [];

    /**
     * GameStatus constructor.
     *
     * @param $boardState
     * @param $playerUnit
     * @param $nextMove
     */
    public function __construct($boardState, $playerUnit, $nextMove)
    {
        $boardStateWithCoords = $this->createBoardStateWithCoords($boardState, $nextMove);
        $playerMoves = $this->filterBoardStateByUnit($boardStateWithCoords, $playerUnit);
        $winnerMoves = $this->getWinnerMoves($playerMoves);

        if ($winnerMoves) {
            $this->winner = $this->createWinner($winnerMoves, $playerUnit);
        }

        if (is_null($winnerMoves)) {
            $botMoves = $this->filterBoardStateByUnit($boardStateWithCoords, GameUnit::getInverseUnit($playerUnit));
            $winnerMoves = $this->getWinnerMoves($botMoves);
            if ($winnerMoves) {
                $this->winner = $this->createWinner($winnerMoves, GameUnit::getInverseUnit($playerUnit));
            }
        }

        if (is_null($winnerMoves)) {
            $availableMoves = $this->filterBoardStateByUnit($boardStateWithCoords, "");
            if (empty($availableMoves)) {
                $this->isTied = true;
            }
        }
    }

    /**
     * @return bool
     */
    public function isTied(): bool
    {
        return $this->isTied;
    }

    /**
     * @return array
     */
    public function getWinner(): array
    {
        return $this->winner;
    }

    /**
     * @param array $boardState
     * @param array $nextMove
     *
     * @return array
     */
    private function createBoardStateWithCoords(array $boardState, array $nextMove)
    {
        $boardStateWithCoords = [];
        foreach ($boardState as $x => $lineState) {
            array_walk($lineState, function (string &$unit, $y) use (&$boardStateWithCoords, $x, $nextMove) {
                if (!empty($nextMove) && $nextMove[0] == $y && $nextMove[1] == $x && $nextMove[2]) {
                    $boardStateWithCoords[] = $nextMove;
                } else {
                    $boardStateWithCoords[] = [$y, $x, $unit];
                }
            });
        }
        return $boardStateWithCoords;
    }


    /**
     * @param array $winnerMoves
     * @param $playerUnit
     *
     * @return array
     */
    private function createWinner(array $winnerMoves, $playerUnit) : array
    {
        return [
            'unit' => $playerUnit,
            'moves' => $winnerMoves,
        ];
    }

    /**
     * @param $playerMoves
     *
     * @return mixed
     */
    private function getWinnerMoves($playerMoves)
    {
        $winnerCombinations = WinnerMoves::getWinnerMoves();
        array_walk($winnerCombinations, function (array $combination) use ($playerMoves, &$winnerMoves) {
            $matchPoints = 0;
            foreach ($playerMoves as $move) {
                array_pop($move);
                if (in_array($move, $combination)) {
                    $matchPoints++;
                }
            }

            if ($matchPoints === 3) {
                $winnerMoves = $combination;
            }
        });

        return $winnerMoves;
    }

    /**
     * @param $boardStateWithCoords
     * @param $playerUnit
     *
     * @return array
     */
    private function filterBoardStateByUnit($boardStateWithCoords, $playerUnit)
    {
        $playerMoves = array_map(
            function ($move) use ($playerUnit) {
                if ($move[2] == $playerUnit) {
                    return $move;
                }
            },
            $boardStateWithCoords
        );

        return array_filter($playerMoves, function ($value) {
            return !is_null($value);
        });
    }
}
