<?php

namespace TicTacToe\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use TicTacToe\Entity\Board;

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

        /**
         * @var ArrayCollection
         */
        $emptyMoves = $this->getAllEmptyMovesFromBoard($board);

        if ($emptyMoves->count() === 0) {
            $board->setCompleted();
        }

        return $board;
    }

    /**
     * @param Board $board
     *
     * @return ArrayCollection|null
     */
    protected function getAllEmptyMovesFromBoard(Board $board): ?ArrayCollection
    {
        /**
         * @var ArrayCollection
         */
        $moves = clone $board->getMoves();

        foreach ($moves as $move) {
            if (!empty($move->getUnit())) {
                $moves->removeElement($move);
            }
        }

        return $moves;
    }
}
