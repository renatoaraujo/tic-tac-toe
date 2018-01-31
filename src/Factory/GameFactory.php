<?php

namespace TicTacToe\Factory;

use TicTacToe\Entity\Board;
use TicTacToe\Entity\Game;
use TicTacToe\Entity\Winner;
use TicTacToe\Util\GameUnit;

/**
 * Class GameFactory
 * @package TicTacToe\Factory
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class GameFactory
{
    /**
     * @param string $playerUnit
     * @param null|Board $board
     * @param null|Winner $winner
     * @param array|null $nextMove
     *
     * @return Game
     */
    public function createGame(
        string $playerUnit,
        ?Board $board = null,
        ?array $nextMove = [],
        ?Winner $winner = null
    ): Game {
        $game = new Game();
        $game->setPlayerUnit($playerUnit);

        if ($board->isCompleted()) {
            $game->setTied();
        }

        if (!is_null($board)) {
            $game->setBoardState($board);
        }

        $game->setNextMove($nextMove);

        return $game;
    }
}
