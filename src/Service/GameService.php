<?php

namespace TicTacToe\Service;

use Doctrine\Common\Collections\ArrayCollection;
use TicTacToe\Entity\Board;
use TicTacToe\Entity\Game;
use TicTacToe\Entity\Move;
use TicTacToe\Factory\BoardFactory;
use TicTacToe\Factory\GameFactory;
use TicTacToe\Factory\MoveFactory;
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
     * @var BoardFactory
     */
    private $boardFactory;

    /**
     * @var MoveFactory
     */
    private $moveFactory;

    /**
     * @var GameFactory
     */
    private $gameFactory;

    /**
     * @var Board
     */
    private $board;

    /**
     * @var array
     */
    private $winner = ['unit' => null, 'moves' => []];

    /**
     * GameService constructor.
     *
     * @param BoardFactory $boardFactory
     * @param MoveFactory $moveFactory
     * @param GameFactory $gameFactory
     */
    public function __construct(
        BoardFactory $boardFactory,
        MoveFactory $moveFactory,
        GameFactory $gameFactory
    ) {
        $this->boardFactory = $boardFactory;
        $this->moveFactory = $moveFactory;
        $this->gameFactory = $gameFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function makeMove(array $boardState, string $playerUnit = 'X'): array
    {
        $moves = $this->moveFactory->createMovesFromBoardState($boardState);
        $this->board = $this->boardFactory->createBoard($moves);

        $movesByPlayer = $this->boardFactory->getBoardMovesGroupedByUnit($this->board, $playerUnit);
        $isPlayerWinner = $this->checkWinner($playerUnit, $movesByPlayer);

        if ($isPlayerWinner) {
            $this->setWinnerUnit($playerUnit);
            return [];
        }

        $availableMoves = $this->boardFactory->getAllEmptyMovesFromBoard($this->board);

        if ($availableMoves->isEmpty()) {
            return [];
        }

        $mostProbableMove = $this->predictNextMove($movesByPlayer, $playerUnit);
        $move = (is_null($mostProbableMove)) ? $availableMoves->first() : $mostProbableMove;
        $move->setUnit(GameUnit::getInverseUnit($playerUnit));

        $movesByBot = $this->boardFactory->getBoardMovesGroupedByUnit($this->board, GameUnit::getInverseUnit($playerUnit));
        $movesByBot->add($move);
        $isBotWinner = $this->checkWinner(GameUnit::getInverseUnit($playerUnit), $movesByBot);
        $this->setWinnerUnit($isBotWinner ? GameUnit::getInverseUnit($playerUnit) : null);

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

        if (!is_null($this->getWinnerUnit())) {
            $filteredCombinations = $this->getFilteredWinnerCombinations($this->board->getMoves(), $this->getWinnerUnit());
            $winnerCombinations = $this->moveFactory->getWinnerMovesCombinations($this->getWinnerUnit());
            array_walk($filteredCombinations, function ($combination, $key) use ($winnerCombinations) {
                if (empty($combination)) {
                    $this->setWinnerMoves($winnerCombinations[$key]);
                }
            });
        }

        return $this->gameFactory->createGame(
            $requestGame->playerUnit,
            $this->board,
            $this->winner,
            $nextMove
        );
    }

    /**
     * @param ArrayCollection $moves
     * @param string $unit
     *
     * @return Move
     */
    protected function predictNextMove(ArrayCollection $moves, string $unit): ?Move
    {
        $winnerCombinations = $this->getFilteredWinnerCombinations($moves, $unit);
        $availableMoves = clone $this->boardFactory->getAllEmptyMovesFromBoard($this->board);
        $availableMoves->forAll(function (int $key, Move $availableMove) use ($unit) {
            return $availableMove->setUnit($unit);
        });

        $mostProbableMove = null;

        foreach ($winnerCombinations as $winnerCombinationKey => &$winnerCombination) {
            array_walk($winnerCombination, function (
                &$combination,
                $key
            ) use (
                &$winnerCombination,
                &$winnerCombinationKey,
                &$winnerCombinations,
                $availableMoves,
                &$mostProbableMove,
                $moves
            ) {
                if (!in_array($combination, $moves->toArray()) && !in_array($combination, $availableMoves->toArray())) {
                    unset($winnerCombinations[$winnerCombinationKey]);
                    return;
                }

                if (!in_array($combination, $availableMoves->toArray())) {
                    unset($winnerCombination[$key]);
                }

                if (count($winnerCombination) === 1) {
                    $mostProbableMove = $combination;
                }
            });

            if (empty($winnerCombination)) {
                unset($winnerCombinations[$winnerCombinationKey]);
            }
        }

        if (is_null($mostProbableMove) && !empty($winnerCombinations)) {
            $mostProbableMove = current(min($winnerCombinations));
        }

        return $mostProbableMove;
    }

    /**
     * @param string $unit
     * @param ArrayCollection $moves
     *
     * @return bool
     */
    protected function checkWinner(string $unit, ArrayCollection $moves): bool
    {
        $filteredCombinations = $this->getFilteredWinnerCombinations($moves, $unit);
        $isWinner = false;
        foreach ($filteredCombinations as $combination) {
            if (empty($combination)) {
                $isWinner = true;
            }
        }
        return $isWinner;
    }

    /**
     * @param string $unit
     */
    protected function setWinnerUnit(?string $unit): void
    {
        $this->winner['unit'] = $unit;
    }

    /**
     * @param array $winnerMoves
     */
    protected function setWinnerMoves(array $winnerMoves): void
    {
        $this->winner['moves'] = $winnerMoves;
    }

    /**
     * @return null|string
     */
    protected function getWinnerUnit(): ?string
    {
        return $this->winner['unit'];
    }

    /**
     * @return array
     */
    protected function getWinnerMoves(): array
    {
        return $this->winner['moves'];
    }

    /**
     * @param ArrayCollection $moves
     * @param string $unit
     *
     * @return array
     */
    protected function getFilteredWinnerCombinations(ArrayCollection $moves, string $unit): array
    {
        $winnerCombinations = $this->moveFactory->getWinnerMovesCombinations($unit);

        array_walk($winnerCombinations, function (&$value, $indexCombination) use (&$winnerCombinations, $moves) {
            foreach ($value as $key => &$combination) {
                if (in_array($combination, $moves->toArray())) {
                    unset($value[$key]);
                }
            }
            $winnerCombinations[$indexCombination] = $value;
        });

        return $winnerCombinations;
    }
}
