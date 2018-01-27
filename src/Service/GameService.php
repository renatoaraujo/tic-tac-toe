<?php

namespace TicTacToe\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use TicTacToe\Entity\Game;

/**
 * Class MoveService
 * @package TicTacToe\Service
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class GameService implements MoveInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * GameService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function makeMove(array $boardState, string $playerUnit = 'X'): array
    {
        return [];
    }

    /**
     * @param string $content
     *
     * @return Game
     */
    public function createGame(string $content): Game
    {
        $requestGame = json_decode($content);
        $moves = $this->container->get('TicTacToe\Factory\MoveFactory')
            ->createMovesFromBoardState($requestGame->boardState);
        $board = $this->container->get('TicTacToe\Factory\BoardFactory')
            ->createBoardWithMoves($moves);
        $game = $this->container->get('TicTacToe\Factory\GameFactory')
            ->createGame($requestGame->playerUnit, $board);
        return $game;
    }
}
