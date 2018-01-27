<?php

namespace TicTacToe\Tests\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use TicTacToe\Factory\BoardFactory;
use TicTacToe\Factory\MoveFactory;

/**
 * Class BoardFactoryTest
 * @package TicTacToe\Tests\Factory
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class BoardFactoryTest extends TestCase
{
    /**
     * @var BoardFactory
     */
    private $factory;

    /**
     * @var ArrayCollection
     */
    private static $staticMoves;

    /**
     * Create static moves for testing
     */
    public static function setUpBeforeClass()
    {
        $boardState = [
            ["X", "O", ""],
            ["X", "O", "O"],
            ["O", "X", "X"]
        ];

        $moveFactory = new MoveFactory();
        self::$staticMoves = $moveFactory->createMovesFromBoardState($boardState);
    }

    public function setUp(): void
    {
        $this->factory = new BoardFactory();
    }

    /**
     * Test board with moves
     */
    public function testCreateBoardWithMoves(): void
    {
        $board = $this->factory->createBoardWithMoves(self::$staticMoves);
        $this->assertAttributeCount(9, 'moves', $board);
    }
}
