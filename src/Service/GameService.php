<?php

namespace TicTacToe\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TicTacToe\Entity\Board;
use TicTacToe\Entity\Game;
use TicTacToe\Entity\Move;
use TicTacToe\Util\GameUnit;

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
     * @var Board
     */
    private $board;

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
        $moves = $this->container->get('TicTacToe\Factory\MoveFactory')->createMovesFromBoardState($boardState);

        $boardFactory = $this->container->get('TicTacToe\Factory\BoardFactory');
        $this->board = $boardFactory->createBoard($moves);

        $availableMoves = $boardFactory->getAllEmptyMovesFromBoard($this->board);

        $move = ($availableMoves->count() > 1) ? $this->predictNextMove($availableMoves) : $availableMoves->first();
        $move->setUnit(GameUnit::getInverseUnit($playerUnit));

        return array_values((array) $move);
    }

    /**
     * @param string $content
     *
     * @return Game
     */
    public function createGame(string $content): Game
    {
        $requestGame = json_decode($content);
        $nextMove = $this->makeMove($requestGame->boardState, $requestGame->playerUnit);

        return $this->container->get('TicTacToe\Factory\GameFactory')
            ->createGame($requestGame->playerUnit, $this->board)
            ->setNextMove($nextMove);
    }

    /**
     * @param ArrayCollection $moves
     *
     * @return Move
     */
    protected function predictNextMove(ArrayCollection $moves): Move
    {
        return $moves->first();
    }
}
