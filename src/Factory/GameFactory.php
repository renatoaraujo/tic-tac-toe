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
    /**
     * @param string $playerUnit
     * @param null|Board $board
     * @param array $winner
     * @param array|null $nextMove
     *
     * @return Game
     */
    public function createGame(
        string $playerUnit,
        ?Board $board = null,
        array $winner = [],
        ?array $nextMove = []
    ): Game {
        $game = new Game();
        $game->setPlayerUnit($playerUnit);
        $game->setBotUnit(GameUnit::getInverseUnit($playerUnit));

        if (!empty($winner)) {
            ($winner['unit'] === $playerUnit) ? $game->setPlayerWinner() : $game->setBotWinner();
            $game->setWinnerMoves($winner['moves']);
        }

        if ($board->isCompleted() && empty($winner)) {
            $game->setTied();
        }

        if (!is_null($board)) {
            $game->setBoardState($board);
        }

        $game->setNextMove($nextMove);

        return $game;
    }
}
