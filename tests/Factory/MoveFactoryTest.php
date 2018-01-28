<?php

namespace TicTacToe\Tests\Factory;

use PHPUnit\Framework\TestCase;
use TicTacToe\Factory\MoveFactory;

/**
 * Class MoveFactoryTest
 * @package TicTacToe\Tests\Factory
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class MoveFactoryTest extends TestCase
{
    /**
     * @var MoveFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new MoveFactory();
    }

    public function testCreateMovesFromBoardState(): void
    {
        $boardState = [
            ["X", "O", ""],
            ["X", "O", "O"],
            ["O",  "X", "X"]
        ];
        $moves = $this->factory->createMovesFromBoardState($boardState);
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $moves);
    }
}
