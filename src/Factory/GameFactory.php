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
        array $winner,
        ?array $nextMove = []
    ): Game {
        $game = new Game();
        $game->setPlayerUnit($playerUnit);

        $winnerUnit = isset($winner['unit']) ? $winner['unit'] : null;
        $winnerMoves = isset($winner['moves']) ? $winner['moves'] : null;

        if (!is_null($winnerUnit)) {
            ($winner['unit'] === $playerUnit) ? $game->setPlayerWinner() : $game->setBotWinner();
            $game->setWinnerMoves($winnerMoves);
        }

        if ($board->isCompleted() && is_null($winnerUnit)) {
            $game->setTied();
        }

        if (!is_null($board)) {
            $game->setBoardState($board);
        }

        $game->setNextMove($nextMove);

        return $game;
    }
}
