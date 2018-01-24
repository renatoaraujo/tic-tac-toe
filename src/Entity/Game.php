<?php

namespace TicTacToe\Entity;

/**
 * Class Game
 * @package TicTacToe\Entity
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class Game implements EntityInterface
{
    /**
     * @var string
     */
    private $botUnit;

    /**
     * @var string
     */
    private $playerUnit;

    /**
     * @var boolean
     */
    private $isTied;

    /**
     * @var boolean
     */
    private $isBotWinner;

    /**
     * @var boolean
     */
    private $isPlayerWinner;

    /**
     * @var Board
     */
    private $board;

    /**
     * @return string
     */
    public function getBotUnit(): string
    {
        return $this->botUnit;
    }

    /**
     * @param string $botUnit
     *
     * @return Game
     */
    public function setBotUnit(string $botUnit): Game
    {
        $this->botUnit = $botUnit;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlayerUnit(): string
    {
        return $this->playerUnit;
    }

    /**
     * @param string $playerUnit
     *
     * @return Game
     */
    public function setPlayerUnit(string $playerUnit): Game
    {
        $this->playerUnit = $playerUnit;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTied(): bool
    {
        return $this->isTied;
    }

    /**
     * @param bool $isTied
     *
     * @return Game
     */
    public function setIsTied(bool $isTied): Game
    {
        $this->isTied = $isTied;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBotWinner(): bool
    {
        return $this->isBotWinner;
    }

    /**
     * @param bool $isBotWinner
     *
     * @return Game
     */
    public function setIsBotWinner(bool $isBotWinner): Game
    {
        $this->isBotWinner = $isBotWinner;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPlayerWinner(): bool
    {
        return $this->isPlayerWinner;
    }

    /**
     * @param bool $isPlayerWinner
     *
     * @return Game
     */
    public function setIsPlayerWinner(bool $isPlayerWinner): Game
    {
        $this->isPlayerWinner = $isPlayerWinner;
        return $this;
    }

    /**
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * @param Board $board
     *
     * @return Game
     */
    public function setBoard(Board $board): Game
    {
        $this->board = $board;
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