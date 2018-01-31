<?php

namespace TicTacToe\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use TicTacToe\Entity\Winner;

/**
 * Class WinnerFactory
 * @package TicTacToe\Factory
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class WinnerFactory
{
    /**
     * @param ArrayCollection $moves
     * @param string $unit
     *
     * @return Winner
     */
    public function createWinner(ArrayCollection $moves, string $unit): Winner
    {
        $winner = new Winner();
        $winner->setUnit($unit);
        $winner->setMoves($moves);
        return $winner;
    }
}
