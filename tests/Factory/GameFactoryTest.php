<?php

namespace TicTacToe\Tests\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use TicTacToe\Entity\Board;
use TicTacToe\Factory\BoardFactory;
use TicTacToe\Factory\GameFactory;
use TicTacToe\Factory\MoveFactory;
use TicTacToe\Util\GameUnit;

class GameFactoryTest extends TestCase
{
    /**
     * @var GameFactory
     */
    private $factory;

    /**
     * @var ArrayCollection
     */
    private static $staticMoves;

    /**
     * @var Board
     */
    private $board;

    /**
     * Create static moves for testing
     */
    public static function setUpBeforeClass()
    {
        $boardState = [
            ["X", "O", "O"],
            ["O", "O", "X"],
            ["X", "X", "X"]
        ];

        $moveFactory = new MoveFactory();
        self::$staticMoves = $moveFactory->createMovesFromBoardState($boardState);
    }

    public function setUp()
    {
        $this->factory = new GameFactory();
        $boardFactory = new BoardFactory();
        $this->board = $boardFactory->createBoard(self::$staticMoves);
    }

    public function testCreateGame(): void
    {
        $game = $this->factory->createGame(GameUnit::X_UNIT, $this->board);
        $this->assertSame(GameUnit::O_UNIT, $game->getBotUnit());
    }

    public function testCreateGameWithBotWinner(): void
    {
        $game = $this->factory->createGame(GameUnit::X_UNIT, $this->board, GameUnit::O_UNIT);
        $this->assertTrue($game->isBotWinner());
        $this->assertTrue(!$game->isPlayerWinner());
        $this->assertTrue(!$game->isTied());
    }

    public function testCreateGameWithPlayerWinner(): void
    {
        $game = $this->factory->createGame(GameUnit::X_UNIT, $this->board, GameUnit::X_UNIT);
        $this->assertTrue($game->isPlayerWinner());
        $this->assertTrue(!$game->isBotWinner());
        $this->assertTrue(!$game->isTied());
    }

    public function testCreateTiedGame(): void
    {
        $game = $this->factory->createGame(GameUnit::X_UNIT, $this->board, null);
        $this->assertTrue($game->isTied());
        $this->assertTrue(!$game->isBotWinner());
        $this->assertTrue(!$game->isPlayerWinner());
    }
}
