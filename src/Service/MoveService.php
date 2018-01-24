<?php

namespace TicTacToe\Service;

/**
 * Class MoveService
 * @package TicTacToe\Service
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class MoveService implements MoveInterface
{
    /**
     * {@inheritdoc}
     */
    public function makeMove(array $boardState, string $playerUnit = 'X'): array
    {
        $this->createMoveObject();
        return [];
    }

    private function createMoveObject(): void
    {}
}