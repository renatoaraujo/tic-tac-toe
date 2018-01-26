<?php

namespace TicTacToe\Factory;

use TicTacToe\Entity\Board;
use TicTacToe\Entity\Game;
use TicTacToe\Util\GameUnit;

/**
 * Class GameFactory
 * @package TicTacToe\Factory
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class GameFactory
{
    public function createGameNewGameWithBoard(string $playerUnit, Board $board): Game
    {
        return $this->createGame($playerUnit, $board);
    }

    /**
     * @param string $playerUnit
     * @param null|Board $board
     *
     * @return Game
     */
    protected function createGame(string $playerUnit, ?Board $board = null): Game
    {
        $game = new Game();
        $game->setPlayerUnit($playerUnit);
        $game->setBotUnit(GameUnit::getInverseUnit($playerUnit));

        if (!is_null($board)) {
            $game->setBoardState($board);
        }

        return $game;
    }
}
