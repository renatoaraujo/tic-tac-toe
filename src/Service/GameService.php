<?php

namespace TicTacToe\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TicTacToe\Entity\Board;
use TicTacToe\Entity\Game;
use TicTacToe\Entity\Move;
use TicTacToe\Factory\BoardFactory;
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
     * @var string
     */
    private $unitWinner;

    /**
     * @var BoardFactory
     */
    private $boardFactory;

    /**
     * GameService constructor.
     *
     * @param ContainerInterface $container
     * @param BoardFactory $boardFactory
     */
    public function __construct(ContainerInterface $container, BoardFactory $boardFactory)
    {
        $this->container = $container;
        $this->boardFactory = $boardFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function makeMove(array $boardState, string $playerUnit = 'X'): array
    {
        $moves = $this->container->get('TicTacToe\Factory\MoveFactory')
            ->createMovesFromBoardState($boardState);
        $this->board = $this->boardFactory->createBoard($moves);

        $movesByPlayer = $this->boardFactory->getBoardMovesGroupedByUnit($this->board, $playerUnit);
        $isPlayerWinner = $this->checkWinner($playerUnit, $movesByPlayer);

        if ($isPlayerWinner) {
            return [];
        }

        $availableMoves = $this->boardFactory->getAllEmptyMovesFromBoard($this->board);

        if ($availableMoves->isEmpty()) {
            return [];
        }

        $move = ($availableMoves->count() > 1) ? $this->predictNextMove($availableMoves) : $availableMoves->first();
        $move->setUnit(GameUnit::getInverseUnit($playerUnit));

        $movesByBot = $this->boardFactory->getBoardMovesGroupedByUnit($this->board, GameUnit::getInverseUnit($playerUnit));
        $movesByBot->add($move);
        $this->checkWinner(GameUnit::getInverseUnit($playerUnit), $movesByBot);

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
            ->createGame($requestGame->playerUnit, $this->board, $this->unitWinner, $nextMove);
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

    /**
     * @param string $playerUnit
     * @param ArrayCollection $moves
     *
     * @return bool
     */
    protected function checkWinner(string $playerUnit, ArrayCollection $moves): bool
    {
        $winnerCombinations = $this->container->get('TicTacToe\Factory\MoveFactory')
            ->getWinnerMovesCombinations($playerUnit);
        $isWinner = false;

        array_walk($winnerCombinations, function (&$value) use (
            $moves,
            &$isWinner,
            $playerUnit
        ) {

            foreach ($value as $key => $combination) {
                if (in_array($combination, $moves->toArray())) {
                    unset($value[$key]);
                }
            }

            if (empty($value)) {
                $isWinner = true;
                $this->setUnitWinner($playerUnit);
            }
        });

        return $isWinner;
    }

    /**
     * @param string $unit
     */
    protected function setUnitWinner(string $unit): void
    {
        $this->unitWinner = $unit;
    }
}
