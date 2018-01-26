<?php

namespace TicTacToe\Tests\Factory;

use PHPUnit\Framework\TestCase;
use TicTacToe\Entity\Board;
use TicTacToe\Factory\GameFactory;
use TicTacToe\Util\GameUnit;

class GameFactoryTest extends TestCase
{
    /**
     * @var GameFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new GameFactory();
    }

    public function testCreateNewGame(): void
    {
        $board = new Board();
        $game = $this->factory->createGameNewGameWithBoard(GameUnit::X_UNIT, $board);
        $this->assertInstanceOf("TicTacToe\Entity\Game", $game);
        $this->assertSame(GameUnit::O_UNIT, $game->getBotUnit());
    }
}