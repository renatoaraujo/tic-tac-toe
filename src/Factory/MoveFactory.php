<?php

namespace TicTacToe\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use TicTacToe\Entity\Move;

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

    protected function createMove(string $unit, int $coordX, int $coordY): Move
    {
        $move = new Move();
        $move->setCoordX($coordX);
        $move->setCoordY($coordY);
        $move->setUnit($unit);
        return $move;
    }
}
