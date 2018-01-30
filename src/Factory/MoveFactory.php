<?php

namespace TicTacToe\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use TicTacToe\Entity\Move;

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
        foreach ($boardState as $lineIndex => $lineState) {
            array_walk($lineState, function (&$v, $k) use ($moves, $lineIndex) {
                $moves->add($this->createMove($v, $lineIndex, $k));
            });
        }

        return $moves;
    }

    /**
     * @param string $unit
     * @param int $coordX
     * @param int $coordY
     *
     * @return Move
     */
    public function createMove(?string $unit, int $coordX, int $coordY): Move
    {
        $move = new Move();
        $move->setCoordX($coordX);
        $move->setCoordY($coordY);

        if (!empty($unit)) {
            $move->setUnit($unit);
        }

        return $move;
    }

    /**
     * @return ArrayCollection
     */
    public function getWinnerMovesCombinations(): ArrayCollection
    {
        $winnerCombinations = new ArrayCollection();
        $winnerCombinations->add(new ArrayCollection([
            new Move(2, 0),
            new Move(1, 1),
            new Move(0, 2),
        ]));

        $winnerCombinations->add(new ArrayCollection([
            new Move(0, 0),
            new Move(1, 1),
            new Move(2, 2),
        ]));

        for ($row = 0; $row < 3; $row++) {
            $winnerCombinations->add(new ArrayCollection([
                new Move($row, 0),
                new Move($row, 1),
                new Move($row, 2),
            ]));
            $winnerCombinations->add(new ArrayCollection([
                new Move(0, $row),
                new Move(1, $row),
                new Move(2, $row),
            ]));
        }

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
            $possibleCombinations = $combination->exists(function (int $combinationKey, Move $combinationMove) use ($availableBoardMoves) {
                return !in_array($combinationMove, $availableBoardMoves->toArray());
            });

            if ($possibleCombinations) {
                return $combination;
            }
        });

        return $possibleCombinations;
    }

    /**
     * @param string $unit
     * @param ArrayCollection $moves
     *
     * @return bool
     */
    public function checkWinner(string $unit, ArrayCollection $moves): bool
    {
        $filteredCombinations = $this->getFilteredWinnerCombinations($moves, $unit);
        $isWinner = false;
        foreach ($filteredCombinations as $combination) {
            if (empty($combination)) {
                $isWinner = true;
            }
        }
        return $isWinner;
    }
}
