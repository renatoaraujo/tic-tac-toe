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
    private $botUnit;

    /**
     * @var string
     */
    private $playerUnit;

    /**
     * @var boolean
     */
    private $isTied = false;

    /**
     * @var boolean
     */
    private $isBotWinner = false;

    /**
     * @var boolean
     */
    private $isPlayerWinner = false;

    /**
     * @var Board
     */
    private $boardState;

    /**
     * @var array
     */
    private $nextMove;

    /**
     * @var array
     */
    private $winnerMoves;

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
     * @return Game
     */
    public function setTied(): Game
    {
        $this->isTied = true;

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
     * @return Game
     */
    public function setBotWinner(): Game
    {
        $this->isBotWinner = true;

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
     * @return Game
     */
    public function setPlayerWinner(): Game
    {
        $this->isPlayerWinner = true;

        return $this;
    }

    /**
     * @return Board
     */
    public function getBoardState(): Board
    {
        return $this->boardState;
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
     * @return array
     */
    public function getWinnerMoves(): array
    {
        return $this->winnerMoves;
    }

    /**
     * @param array $winnerMoves
     *
     * @return Game
     */
    public function setWinnerMoves(array $winnerMoves): Game
    {
        $this->winnerMoves = $winnerMoves;

        return $this;
    }
}
