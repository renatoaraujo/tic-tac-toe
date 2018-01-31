<?php

namespace TicTacToe\Entity;

/**
 * Class Game
 * @package TicTacToe\Entity
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class Game
{
    /**
     * @var string
     */
    private $playerUnit;

    /**
     * @var boolean
     */
    private $isTied = false;

    /**
     * @var Board
     */
    private $boardState;

    /**
     * @var array
     */
    private $nextMove;

    /**
     * @var Winner
     */
    private $winner;

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
     * @return Game
     */
    public function setTied(): Game
    {
        $this->isTied = true;

        return $this;
    }

    /**
     * @param Board $boardState
     *
     * @return Game
     */
    public function setBoardState(Board $boardState): Game
    {
        $this->boardState = $boardState;

        return $this;
    }

    /**
     * @return array
     */
    public function getNextMove(): array
    {
        return $this->nextMove;
    }

    /**
     * @param array $nextMove
     *
     * @return Game
     */
    public function setNextMove(array $nextMove): Game
    {
        $this->nextMove = $nextMove;

        return $this;
    }

    /**
     * @return Winner
     */
    public function getWinner(): ?Winner
    {
        return $this->winner;
    }

    /**
     * @param Winner $winner
     *
     * @return Game
     */
    public function setWinner(Winner $winner): Game
    {
        $this->winner = $winner;

        return $this;
    }
}
