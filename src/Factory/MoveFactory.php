<?php

namespace TicTacToe\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use TicTacToe\Entity\Move;
use TicTacToe\Util\WinnerMoves;

/**
 * Class MoveFactory
 * @package TicTacToe\Factory
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class MoveFactory
{
    /**
     * @param array $boardState
     *
     * @return ArrayCollection
     */
    public function createMovesFromBoardState(array $boardState): ArrayCollection
    {
        $moves = new ArrayCollection();
        foreach ($boardState as $x => $lineState) {
            array_walk($lineState, function (string &$unit, $y) use (&$moves, $x) {
                $moves->add($this->createMove($y, $x, $unit));
            });
        }

        return $moves;
    }

    /**
     * @param int $coordY
     * @param int $coordX
     * @param null|string $unit
     *
     * @return Move
     */
    public function createMove(int $coordY, int $coordX, ?string $unit = null): Move
    {
        $move = new Move();
        $move->setCoordY($coordY);
        $move->setCoordX($coordX);

        if (!empty($unit)) {
            $move->setUnit($unit);
        }

        return $move;
    }

    /**
     * @return ArrayCollection
     */
    protected function getWinnerMovesCombinations(): ArrayCollection
    {
        $winnerMoves = WinnerMoves::getWinnerMoves();
        $winnerCombinations = new ArrayCollection();

        array_walk($winnerMoves, function ($combination) use (&$winnerCombinations) {
            $combinationColletion = new ArrayCollection();
            foreach ($combination as $move) {
                $combinationColletion->add($this->createMove(...$move));
            }
            $winnerCombinations->add($combinationColletion);
        });

        return $winnerCombinations;
    }

    /**
     * @param ArrayCollection $availableBoardMoves
     *
     * @return ArrayCollection
     */
    public function getFilteredWinnerCombinations(ArrayCollection $availableBoardMoves): ArrayCollection
    {
        $winnerCombinations = $this->getWinnerMovesCombinations();
        $possibleCombinations = $winnerCombinations->filter(function (ArrayCollection $combination) use (
            $availableBoardMoves,
            &$winnerCombinations
        ) {
            $isAvailableCombination = $combination->exists(function (int $combinationKey, Move $combinationMove) use ($availableBoardMoves) {
                return !in_array($combinationMove, $availableBoardMoves->toArray());
            });

            if ($isAvailableCombination) {
                return $combination;
            }
        });

        return $possibleCombinations;
    }
}
