<?php

namespace TicTacToe\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Winner
 * @package TicTacToe\Entity
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class Winner
{
    /**
     * @var string
     */
    private $unit;

    /**
     * @var ArrayCollection
     */
    private $moves;

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    /**
     * @return ArrayCollection
     */
    public function getMoves(): ArrayCollection
    {
        return $this->moves;
    }

    /**
     * @param ArrayCollection $moves
     */
    public function setMoves(ArrayCollection $moves): void
    {
        $this->moves = $moves;
    }
}
