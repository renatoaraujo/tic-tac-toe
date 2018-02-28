<?php

namespace TicTacToe\Service;

use Doctrine\Common\Collections\ArrayCollection;
use TicTacToe\Entity\Board;
use TicTacToe\Entity\Move;
use TicTacToe\Exception\EmptyAvailableMovesException;
use TicTacToe\Factory\BoardFactory;
use TicTacToe\Factory\MoveFactory;
use TicTacToe\Util\GameUnit;

/**
 * Class MoveService
 * @package TicTacToe\Service
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class MoveService implements MoveInterface
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
     * @var Board
     */
    private $board;

    /**
     * MoveService constructor.
     *
     * @param BoardFactory $boardFactory
     * @param MoveFactory $moveFactory
     */
    public function __construct(
        BoardFactory $boardFactory,
        MoveFactory $moveFactory
    ) {
        $this->boardFactory = $boardFactory;
        $this->moveFactory = $moveFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function makeMove(array $boardState, string $playerUnit = 'X'): array
    {
        $moves = $this->moveFactory->createMovesFromBoardState($boardState);
        $this->board = $this->boardFactory->createBoard($moves);
        $playerCombinations = $this->getUnitCombinations($playerUnit);
        $botCombinations = $this->getUnitCombinations(GameUnit::getInverseUnit($playerUnit));
        $nextMove = $this->getNextMove($playerCombinations, $botCombinations, $playerUnit);

        return ($nextMove instanceof Move) ? array_values((array) $nextMove) : [];
    }

    /**
     * @param ArrayCollection $playerCombinations
     * @param ArrayCollection $botCombinations
     * @param string $playerUnit
     *
     * @return null|Move
     */
    protected function getNextMove(ArrayCollection $playerCombinations, ArrayCollection $botCombinations, string $playerUnit): ?Move
    {
        try {
            $nextMove = $this->predictNexMove($playerCombinations, $botCombinations);

            if (!isset($nextMove)) {
                $nextMove = $this->getStarterMove();
            }

            if ($nextMove) {
                $this->board->getMoves()->forAll(function (int $moveKey, Move $move) use (&$nextMove, $playerUnit) {
                    if ($move->getCoordX() == $nextMove->getCoordX() && $move->getCoordY() == $nextMove->getCoordY()) {
                        $move->setUnit(GameUnit::getInverseUnit($playerUnit));
                        $nextMove = $move;
                    }
                    return true;
                });
            }
        } catch (EmptyAvailableMovesException $exception) {
            $nextMove = null;
        }

        return $nextMove;
    }

    /**
     * @param $playerCombinations
     * @param $botCombinations
     *
     * @return mixed|null|Move
     * @throws EmptyAvailableMovesException
     */
    protected function predictNexMove($playerCombinations, $botCombinations)
    {
        $nextMovesByPlayer = $this->getNextMovesByUnit($playerCombinations);
        $willPlayerWin = ($nextMovesByPlayer->first() && $nextMovesByPlayer->first()->count() == 1);
        $nextMovesByBot = $this->getNextMovesByUnit($botCombinations);
        $willBotWin = ($nextMovesByBot->first() && $nextMovesByBot->first()->count() == 1);

        if ($willBotWin) {
            $nextMove = $this->filterNextMove($nextMovesByBot);
        }

        if ($willPlayerWin && !$willBotWin) {
            $nextMove = $this->filterNextMove($nextMovesByBot);
        }

        return isset($nextMove) ? $nextMove : null;
    }

    /**
     * @param ArrayCollection $nextMoves
     *
     * @return null|Move
     */
    protected function filterNextMove(ArrayCollection $nextMoves): ?Move
    {
        if (!$nextMoves->isEmpty()) {
            $mostProbableMove = min($nextMoves->toArray());
            if ($mostProbableMove) {
                return $mostProbableMove->first();
            }
        }

        dump($nextMoves);
        die;

        $nextMoves->filter(function (ArrayCollection $arrayCollection) use (&$nextMove) {
            return $arrayCollection->first();
        });

        return $nextMove;
    }

    /**
     * @param ArrayCollection $unitCombination
     *
     * @return ArrayCollection
     * @throws EmptyAvailableMovesException
     */
    protected function getNextMovesByUnit(ArrayCollection $unitCombination) : ArrayCollection
    {
        $availableMoves = $this->boardFactory->getAllEmptyMovesFromBoard($this->board);
        $possibleCombinations = $this->moveFactory->getFilteredWinnerCombinations($availableMoves);
        if ($availableMoves->isEmpty()) {
            throw new EmptyAvailableMovesException();
        }

        return $this->getPossibleCombinations($possibleCombinations, $unitCombination, $availableMoves);
    }

    /**
     * @param ArrayCollection $possibleCombinations
     * @param ArrayCollection $unitCombination
     * @param ArrayCollection $availableMoves
     *
     * @return ArrayCollection
     */
    protected function getPossibleCombinations(
        ArrayCollection $possibleCombinations,
        ArrayCollection $unitCombination,
        ArrayCollection $availableMoves
    ): ArrayCollection {
        $combinations = $possibleCombinations->filter(function (ArrayCollection $combination) use ($unitCombination, $availableMoves) {
            foreach ($unitCombination as $comb) {
                $match = true;
                for ($i = 0; $i <= $combination->count(); $i++) {
                    if (is_null($comb->get($i))) {
                        continue;
                    }
                    if ($comb->get($i) != $combination->get($i)) {
                        $match = false;
                    }
                }
                if ($match) {
                    $combination->exists(function (int $combinationKey, Move $combinationMove) use (&$combination, $availableMoves) {
                        if (!in_array($combinationMove, $availableMoves->toArray())) {
                            $combination->remove($combinationKey);
                        }
                    });
                    if (!$combination->isEmpty()) {
                        return $combination;
                    }
                }
            }
        });

        return $this->sortPossibleCombinations($combinations);
    }

    /**
     * @return Move
     */
    protected function getStarterMove(): ?Move
    {
        $availableMoves = $this->boardFactory->getAllEmptyMovesFromBoard($this->board);
        $starterMove = $availableMoves->filter(function (Move $move) {
            if ($move->getCoordY() === 1 && $move->getCoordX() === 1 && empty($move->getUnit())) {
                return $move;
            }
        });

        if (!$starterMove->first()) {
            return $availableMoves->first();
        }

        return $starterMove->first() ?: null;
    }

    /**
     * @param string $unit
     *
     * @return ArrayCollection
     */
    protected function getUnitCombinations(string $unit): ArrayCollection
    {
        $availableMoves = $this->boardFactory->getAllEmptyMovesFromBoard($this->board);
        $possibleCombinations = $this->moveFactory->getFilteredWinnerCombinations($availableMoves);
        $possibleCombinations->exists(function (
            int $combinationKey,
            ArrayCollection $combination
        ) use (
            $unit,
            &$possibleCombinations,
            $availableMoves
        ) {
            foreach ($combination as $comb) {
                if (!in_array($comb, $availableMoves->toArray())) {
                    if (!$this->unitHasMove($comb, $unit)) {
                        $possibleCombinations->remove($combinationKey);
                    }
                }
            }
        });

        return $this->sortPossibleCombinations($possibleCombinations);
    }

    /**
     * @param ArrayCollection $possibleCombinations
     *
     * @return ArrayCollection
     */
    protected function sortPossibleCombinations(ArrayCollection $possibleCombinations): ArrayCollection
    {
        $iterator = $possibleCombinations->getIterator();
        $iterator->uasort(function (ArrayCollection $first, ArrayCollection $second) {
            if ($first->count() == $second->count()) {
                return 0;
            }
            return ($first->count() < $second->count()) ? -1 : 1;
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * @param Move $combinationMove
     * @param string $unit
     *
     * @return bool
     */
    protected function unitHasMove(Move $combinationMove, string $unit)
    {
        $movesByUnit = $this->boardFactory->getBoardMovesGroupedByUnit($this->board, $unit);
        return $movesByUnit->exists(function ($key, Move $move) use ($combinationMove) {
            if ($move->getCoordY() === $combinationMove->getCoordY() && $move->getCoordX() === $combinationMove->getCoordX()) {
                return true;
            }
            return false;
        });
    }
}
