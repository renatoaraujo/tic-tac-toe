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
use TicTacToe\Factory\WinnerFactory;
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
     * @var WinnerFactory
     */
    private $winnerFactory;

    /**
     * @var Board
     */
    private $board;

    /**
     * GameService constructor.
     *
     * @param BoardFactory $boardFactory
     * @param MoveFactory $moveFactory
     * @param GameFactory $gameFactory
     * @param WinnerFactory $winnerFactory
     */
    public function __construct(
        BoardFactory $boardFactory,
        MoveFactory $moveFactory,
        GameFactory $gameFactory,
        WinnerFactory $winnerFactory
    ) {
        $this->boardFactory = $boardFactory;
        $this->moveFactory = $moveFactory;
        $this->gameFactory = $gameFactory;
        $this->winnerFactory = $winnerFactory;
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
        return $this->gameFactory->createGame($requestGame->playerUnit, $this->board, $nextMove);
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
     * @param ArrayCollection $combinations
     *
     * @return bool
     */
    public function checkGameWinner(ArrayCollection $combinations)
    {
        foreach ($combinations->toArray() as $combination) {
            if ($combination->count() === 3) {
                return true;
            }
        }
        return false;
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
            $nextMovesByPlayer = $this->getNextMovesByUnit($playerCombinations);
            $nextMove = $this->filterNextMove($nextMovesByPlayer);
            $nextMovesByBot = $this->getNextMovesByUnit($botCombinations);

            if (!$nextMovesByBot->isEmpty()) {
                $nextMove = $this->filterNextMove($nextMovesByBot, true);
            }

            if (is_null($nextMove)) {
                $nextMove = $this->getStaterMove();
            }

            $this->board->getMoves()->forAll(function (int $moveKey, Move $move) use (&$nextMove, $playerUnit) {
                if ($move->getCoordX() == $nextMove->getCoordX() && $move->getCoordY() == $nextMove->getCoordY()) {
                    $move->setUnit(GameUnit::getInverseUnit($playerUnit));
                    $nextMove = $move;
                }
                return true;
            });
        } catch (EmptyAvailableMovesException $exception) {
            $nextMove = null;
        }

        return $nextMove;
    }

    /**
     * @param ArrayCollection $nextMoves
     * @param bool $isFromBot
     *
     * @return mixed
     */
    protected function filterNextMove(ArrayCollection $nextMoves, bool $isFromBot = false)
    {
        $nextMoves->filter(function (ArrayCollection $arrayCollection) use (&$nextMove, $isFromBot) {
            if ($arrayCollection->count() === 1) {
                $nextMove = $arrayCollection->first();
            } elseif (is_null($nextMove)) {
                if ($isFromBot) {
                    $nextMove = min($arrayCollection->toArray());
                }
            }
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
        return $possibleCombinations->filter(function (ArrayCollection $combination) use ($unitCombination, $availableMoves) {
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
    }

    /**
     * @return Move
     */
    protected function getStaterMove(): Move
    {
        $availableMoves = $this->boardFactory->getAllEmptyMovesFromBoard($this->board);
        $starterMove = $availableMoves->filter(function (Move $move) {
            if ($move->getCoordY() === 1 && $move->getCoordX() === 1 && empty($move->getUnit())) {
                return $move;
            }
        });
        return $starterMove->first();
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
            $possibleCombinations,
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
            return ($first->count() > $second->count()) ? -1 : 1;
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
