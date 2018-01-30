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
     * @param string $unit
     */
    public function setWinnerUnit(?string $unit): void
    {
        $this->winner['unit'] = $unit;
    }

    /**
     * @param ArrayCollection $winnerMoves
     */
    public function setWinnerMoves(ArrayCollection $winnerMoves): void
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
     * @param string $content
     *
     * @return Game
     */
    public function createGame(string $content): Game
    {
        $requestGame = json_decode($content);
        $nextMove = $this->makeMove($requestGame->boardState, $requestGame->playerUnit);
        return $this->gameFactory->createGame($requestGame->playerUnit, $this->board, $this->winner, $nextMove);
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
        dump($combinations);die;
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
            $nextMovesByPlayer->filter(function(ArrayCollection $arrayCollection) use (&$nextMove) {
                if ($arrayCollection->count() === 1) {
                    $nextMove = $arrayCollection->first();
                } elseif (is_null($nextMove)) {
                    $nextMove = $arrayCollection->first();
                }
            });

            $nextMovesByBot = $this->getNextMovesByUnit($botCombinations);
            if (!$nextMovesByBot->isEmpty()) {
                $nextMovesByBot->filter(function(ArrayCollection $arrayCollection) use (&$nextMove) {
                    if ($arrayCollection->count() === 1) {
                        $nextMove = $arrayCollection->first();
                    } elseif (is_null($nextMove)) {
                        $nextMove = min($arrayCollection->toArray());
                    }
                });
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
     * @param $unitCombination
     *
     * @return ArrayCollection
     * @throws EmptyAvailableMovesException
     */
    protected function getNextMovesByUnit($unitCombination) : ArrayCollection
    {
        $availableMoves = $this->boardFactory->getAllEmptyMovesFromBoard($this->board);
        $possibleCombinations = $this->moveFactory->getFilteredWinnerCombinations($availableMoves);

        if ($availableMoves->isEmpty()) {
            throw new EmptyAvailableMovesException();
        }

        $nextMove = $possibleCombinations->filter(function (ArrayCollection $combination) use ($unitCombination, $availableMoves) {
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
                    $combination->exists(function (
                        int $combinationKey,
                        Move $combinationMove
                    ) use (
                        &$combination,
                        $availableMoves
                    ) {
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

        return $nextMove;
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

        $possibleCombinations->exists(function(int $combinationKey, ArrayCollection $combination) use (
            $unit, $possibleCombinations, $availableMoves) {
            foreach ($combination as $comb) {
                if (!in_array($comb, $availableMoves->toArray())) {
                    if (!$this->unitHasMove($comb, $unit)) {
                        $possibleCombinations->remove($combinationKey);
                    }
                }
            }
        });

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
