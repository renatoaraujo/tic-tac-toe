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
     * @param null|string $unitWinner
     * @param bool $isTied
     *
     * @return Game
     */
    public function createGame(
        string $playerUnit,
        ?Board $board = null,
        ?string $unitWinner = null,
        bool $isTied = false
    ): Game {
        $game = new Game();
        $game->setPlayerUnit($playerUnit);
        $game->setBotUnit(GameUnit::getInverseUnit($playerUnit));

        if (!is_null($unitWinner) && !$isTied) {
            ($unitWinner === $playerUnit) ? $game->setPlayerWinner() : $game->setBotWinner();
        }

        if ($isTied) {
            $game->setTied();
        }

        if (!is_null($board)) {
            $game->setBoardState($board);
        }

        return $game;
    }
}
