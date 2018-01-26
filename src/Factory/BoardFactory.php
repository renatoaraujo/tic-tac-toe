<?php

namespace TicTacToe\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use TicTacToe\Entity\Board;

class BoardFactory
{
    /**
     * @param ArrayCollection $moves
     *
     * @return Board
     */
    public function createBoardWithMoves(ArrayCollection $moves): Board
    {
        return $this->createBoard($moves);
    }

    /**
     * @param ArrayCollection|null $moves
     *
     * @return Board
     */
    protected function createBoard(?ArrayCollection $moves = null): Board
    {
        return new Board($moves);
    }
}
