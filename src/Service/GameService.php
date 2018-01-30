<?php

namespace TicTacToe\Service;

use Doctrine\Common\Collections\ArrayCollection;
use TicTacToe\Entity\Board;
use TicTacToe\Entity\Game;
use TicTacToe\Entity\Move;
use TicTacToe\Exception\EmptyAvailableMovesException;
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
     * @var ArrayCollection
     */
    private $movesByPlayer;

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
        $this->movesByPlayer = $this->boardFactory->getBoardMovesGroupedByUnit($this->board, $playerUnit);

        $nextMove = $this->getNextMove($playerUnit);

        return array_values((array) $nextMove);
    }

    /**
     * @param string $playerUnit
     *
     * @return null|Move
     */
    protected function getNextMove(string $playerUnit): ?Move
    {
        try {
            $predictedMove = $this->predictNextMove($playerUnit);
            $nextMove = $this->board->getMoves()->filter(function (Move $move) use ($predictedMove, $playerUnit) {
                if ($move == $predictedMove) {
                    $move->setUnit(GameUnit::getInverseUnit($playerUnit));
                    return $move;
                }
            });

            return $nextMove->first();
        } catch (EmptyAvailableMovesException $exception) {
            return null;
        }
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
        $this->setWinner($requestGame->playerUnit);

        return $this->gameFactory->createGame($requestGame->playerUnit, $this->board, $this->winner, $nextMove);
    }

    /**
     * @return Move
     * @throws EmptyAvailableMovesException
     */
    protected function predictNextMove(): Move
    {
        $availableMoves = $this->boardFactory->getAllEmptyMovesFromBoard($this->board);

        if ($availableMoves->isEmpty()) {
            throw new EmptyAvailableMovesException();
        }

        $mostProbableMove = $this->getMiddleMove($availableMoves)->first();

        if (!$mostProbableMove) {
            $possibleCombinations = $this->moveFactory
                ->getFilteredWinnerCombinations($availableMoves, $this->movesByPlayer);

            $availableNextMoves = $possibleCombinations->filter(function (ArrayCollection &$combination) use (
                $availableMoves
            ) {
                $isFromBot = $combination->exists(function (
                    int $combinationKey,
                    Move $combinationMove
                ) use (
                    &$combination,
                    $availableMoves
                ) {
                    if (!in_array($combinationMove, $availableMoves->toArray())) {
                        $combination->remove($combinationKey);
                        return !$this->isMoveByPlayer($combinationMove);
                    }
                });

                if (!$isFromBot) {
                    return $combination;
                }
            });

            if (!$availableNextMoves->isEmpty()) {
                $iterator = $availableNextMoves->getIterator();
                $iterator->uasort(function (ArrayCollection $first, ArrayCollection $second) {
                    if ($first->count() == $second->count()) {
                        return 0;
                    }

                    return ($first->count() < $second->count()) ? -1 : 1;
                });

                $mostProbableMove = ($iterator->current()->isEmpty()) ? $availableMoves->first() : $iterator->current()->isEmpty();
            }
        }

        return $mostProbableMove;
    }

    /**
     * @param Move $move
     *
     * @return bool
     */
    protected function isMoveByPlayer(Move $move): bool
    {
        foreach ($this->movesByPlayer as $moveByPlayer) {
            if ($move->getCoordY() === $moveByPlayer->getCoordY() && $move->getCoordX() === $moveByPlayer->getCoordX()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $winnerCombination
     * @param ArrayCollection $availableMoves
     *
     * @return array
     */
    protected function filterWinnerCombinations(array $winnerCombination, ArrayCollection $availableMoves)
    {
        array_walk($winnerCombination, function ($combination, $key) use (&$winnerCombination, $availableMoves) {
            if (!in_array($combination, $availableMoves->toArray())) {
                if (!in_array($combination, $this->movesByPlayer->toArray())) {
                    unset($winnerCombination);

                    return;
                }
                unset($winnerCombination[$key]);
            }

            if (count($winnerCombination) === 1) {
                dump($combination);
            }
        });

        return $winnerCombination;
    }

    /**
     * @param ArrayCollection $availableMoves
     *
     * @return ArrayCollection
     */
    protected function getMiddleMove(ArrayCollection $availableMoves): ArrayCollection
    {
        return $availableMoves->filter(function (Move $move) {
            if ($move->getCoordY() === 1 && $move->getCoordX() === 1 && empty($move->getUnit())) {
                return $move;
            }
        });
    }

    /**
     * @param string $unit
     */
    public function setWinnerUnit(?string $unit): void
    {
        $this->winner['unit'] = $unit;
    }

    /**
     * @param array $winnerMoves
     */
    public function setWinnerMoves(array $winnerMoves): void
    {
        $this->winner['moves'] = $winnerMoves;
    }

    /**
     * @return null|string
     */
    public function getWinnerUnit(): ?string
    {
        return $this->winner['unit'];
    }

    /**
     * @return array
     */
    public function getWinnerMoves(): array
    {
        return $this->winner['moves'];
    }

    /**
     * @return array
     */
    public function getWinner(): array
    {
        return $this->winner;
    }

    /**
     * @param string $playerUnit
     */
    public function setWinner(string $playerUnit): void
    {
        if ($this->moveFactory->checkWinner($playerUnit, $this->movesByPlayer)) {
            $this->setWinnerUnit($playerUnit);
        }

        if (is_null($this->getWinnerUnit())) {
            $movesByBot = $this->boardFactory->getBoardMovesGroupedByUnit(
                $this->board,
                GameUnit::getInverseUnit($playerUnit)
            );
            if ($this->moveFactory->checkWinner(GameUnit::getInverseUnit($playerUnit), $movesByBot)) {
                $this->setWinnerUnit(GameUnit::getInverseUnit($playerUnit));
            }
        }

        if (!is_null($this->getWinnerUnit())) {
            $filteredCombinations = $this->moveFactory->getFilteredWinnerCombinations(
                $this->board->getMoves(),
                $this->getWinnerUnit()
            );
            $winnerCombinations = $this->moveFactory->getWinnerMovesCombinations($this->getWinnerUnit());
            array_walk($filteredCombinations, function ($combination, $key) use ($winnerCombinations) {
                if (empty($combination)) {
                    foreach ($winnerCombinations[$key] as &$winnerCombination) {
                        $winnerCombination = array_values((array) $winnerCombination);
                    }
                    $this->setWinnerMoves($winnerCombinations[$key]);
                }
            });
        }
    }
}
