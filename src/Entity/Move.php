<?php

namespace TicTacToe\Entity;

/**
 * Class Move
 * @package TicTacToe\Entity
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class Move
{
    /**
     * @var integer
     */
    private $coordY;

    /**
     * @var integer
     */
    private $coordX;

    /**
     * @var string
     */
    private $unit;

    /**
     * Move constructor.
     *
     * @param int|null $coordY
     * @param int|null $coordX
     * @param null|string $unit
     */
    public function __construct(?int $coordY = null, ?int $coordX = null, ?string $unit = null)
    {
        if (!is_null($coordY)) {
            $this->coordY = $coordY;
        }

        if (!is_null($coordX)) {
            $this->coordX = $coordX;
        }

        if (!is_null($unit)) {
            $this->unit = $unit;
        }
    }

    /**
     * @return int
     */
    public function getCoordY(): int
    {
        return $this->coordY;
    }

    /**
     * @param int $coordY
     *
     * @return Move
     */
    public function setCoordY(int $coordY): Move
    {
        $this->coordY = $coordY;
        return $this;
    }

    /**
     * @return int
     */
    public function getCoordX(): int
    {
        return $this->coordX;
    }

    /**
     * @param int $coordX
     *
     * @return Move
     */
    public function setCoordX(int $coordX): Move
    {
        $this->coordX = $coordX;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     *
     * @return Move
     */
    public function setUnit(string $unit): Move
    {
        $this->unit = $unit;
        return $this;
    }
}
