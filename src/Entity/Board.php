<?php

namespace TicTacToe\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Board
 * @package TicTacToe\Entity
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class Board implements EntityInterface
{
    /**
     * @var ArrayCollection
     */
    private $moves;

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

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
