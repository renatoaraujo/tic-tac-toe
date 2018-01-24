<?php

namespace TicTacToe\Entity;

/**
 * Class Move
 * @package TicTacToe\Entity
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class Move implements EntityInterface
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
     * @return int
     */
    public function getCoordY(): int
    {
        return $this->coordY;
    }

    /**
     * @param int $coordY
     */
    public function setCoordY(int $coordY): void
    {
        $this->coordY = $coordY;
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
     */
    public function setCoordX(int $coordX): void
    {
        $this->coordX = $coordX;
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
     */
    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}