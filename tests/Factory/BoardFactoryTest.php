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
    public function testCreateBoard(): void
    {
        $arrayCollection = $this->createMock('Doctrine\Common\Collections\ArrayCollection');
        $moveFactory = $this->createMock('TicTacToe\Factory\MoveFactory');
        $moveFactory->expects($this->any())
            ->method('createMovesFromBoardState')
            ->with([
                ["X", "O", "O"],
                ["X", "O", "O"],
                ["O", "X", "X"],
            ])
            ->will($this->returnValue($arrayCollection));

        $boardFactory = $this->getMockBuilder('TicTacToe\Factory\BoardFactory')
            ->getMock();

        $this->assertInstanceOf("TicTacToe\Entity\Board", $boardFactory->createBoard($arrayCollection));
        $this->assertInstanceOf(
            "Doctrine\Common\Collections\ArrayCollection",
            $boardFactory->createBoard($arrayCollection)->getMoves()
        );
    }
}
