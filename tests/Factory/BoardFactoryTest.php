<?php

namespace TicTacToe\Tests\Factory;

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
     * @const array boardstate sample
     */
    private const BOARD_STATE = [
        ["X", "O", ""],
        ["X", "O", "O"],
        ["O", "X", "X"]
    ];

    public function setUp(): void
    {
        $this->factory = new BoardFactory();
    }

    public function testCreateBoardWithMoves(): void
    {
        $moveFactory = new MoveFactory();
        $moves = $moveFactory->createMovesFromBoardState(self::BOARD_STATE);
        $board = $this->factory->createBoardWithMoves($moves);
        $this->assertAttributeCount(9, 'moves', $board);
    }
}
