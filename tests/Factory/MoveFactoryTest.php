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
    public function testCreateMovesFromBoardState(): void
    {
        $moveFactory = $this->createMock('TicTacToe\Factory\MoveFactory');
        $boardState = [
            ["X", "O", "O"],
            ["X", "O", "O"],
            ["O", "X", "X"],
        ];
        $this->assertInstanceOf("Doctrine\Common\Collections\ArrayCollection", $moveFactory->createMovesFromBoardState($boardState));
    }

    public function testCreateMove()
    {
        $moveFactory = $this->createMock('TicTacToe\Factory\MoveFactory');
        $this->assertInstanceOf('TicTacToe\Entity\Move', $moveFactory->createMove(1, 2, 'O'));
    }
}
