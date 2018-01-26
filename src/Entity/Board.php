<?php

namespace TicTacToe\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Board
 * @package TicTacToe\Entity
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class Board
{
    /**
     * @var ArrayCollection
     */
    private $moves;

    public function __construct(?ArrayCollection $moves = null)
    {
        if (!is_null($moves)) {
            $this->moves = $moves;
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMoves(): ArrayCollection
    {
        return $this->moves;
    }

    /**
     * @param Move $moves
     *
     * @return Board
     */
    public function addMove(Move $moves): Board
    {
        $this->moves->add($moves);
        return $this;
    }
}
