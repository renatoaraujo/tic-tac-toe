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
        $move->setUnit($unit);

        return $move;
    }

    /**
     * @param null|string $unit
     *
     * @return array
     */
    public function getWinnerMovesCombinations(?string $unit = null): array
    {
        $winnerCombinations = [];
        $winnerCombinations[] = [
            new Move(2, 0, $unit),
            new Move(1, 1, $unit),
            new Move(0, 2, $unit),
        ];

        $winnerCombinations[] = [
            new Move(0, 0, $unit),
            new Move(1, 1, $unit),
            new Move(2, 2, $unit),
        ];

        for ($row = 0; $row < 3; $row++) {
            $winnerCombinations[] = [
                new Move($row, 0, $unit),
                new Move($row, 1, $unit),
                new Move($row, 2, $unit),
            ];
            $winnerCombinations[] = [
                new Move(0, $row, $unit),
                new Move(1, $row, $unit),
                new Move(2, $row, $unit),
            ];
        }

        return $winnerCombinations;
    }


    /**
     * @param ArrayCollection $moves
     * @param string $unit
     *
     * @return array
     */
    public function getFilteredWinnerCombinations(ArrayCollection $moves, string $unit): array
    {
        $winnerCombinations = $this->getWinnerMovesCombinations($unit);

        array_walk($winnerCombinations, function (&$value, $indexCombination) use (&$winnerCombinations, $moves) {
            foreach ($value as $key => &$combination) {
                if (in_array($combination, $moves->toArray())) {
                    unset($value[$key]);
                }
            }
            $winnerCombinations[$indexCombination] = $value;
        });

        return $winnerCombinations;
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
