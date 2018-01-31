<?php

namespace TicTacToe\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use TicTacToe\Entity\Board;
use TicTacToe\Entity\Move;

/**
 * Class BoardFactory
 * @package TicTacToe\Factory
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class BoardFactory
{
    /**
     * @param ArrayCollection|null $moves
     *
     * @return Board
     */
    public function createBoard(ArrayCollection $moves): Board
    {
        $board = new Board($moves);

        $emptyMoves = $this->getAllEmptyMovesFromBoard($board);

        if ($emptyMoves->count() === 0) {
            $board->setCompleted();
        }

        return $board;
    }

    /**
     * @param Board $board
     *
     * @return ArrayCollection
     */
    public function getAllEmptyMovesFromBoard(Board $board): ArrayCollection
    {
        /**
         * @var ArrayCollection
         */
        $moves = clone $board->getMoves();

        if ($moves->toArray()) {
            foreach ($moves->toArray() as $move) {
                if (!empty($move->getUnit())) {
                    $moves->removeElement($move);
                }
            }
        }


        return $moves;
    }

    /**
     * @param Board $board
     * @param string $unit
     *
     * @return ArrayCollection|null
     */
    public function getBoardMovesGroupedByUnit(Board $board, string $unit): ?ArrayCollection
    {
        /**
         * @var ArrayCollection
         */
        $moves = clone $board->getMoves();

        foreach ($moves as $move) {
            if ($move->getUnit() != $unit) {
                $moves->removeElement($move);
            }
        }

        return $moves;
    }

    /**
     * @param Board $board
     * @param $move
     */
    public function updateBoard(Board $board, $move)
    {
        $emptyMoves = $this->getAllEmptyMovesFromBoard($board);

        if ($emptyMoves->count() === 0) {
            $board->setCompleted();
        }
    }
}
