<?php

namespace TicTacToe\Exception;

/**
 * Class EmptyAvailableMovesException
 * @package TicTacToe\Exception
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class EmptyAvailableMovesException extends \Exception
{
    /**
     * EmptyAvailableMovesException constructor.
     */
    public function __construct()
    {
        parent::__construct("No more empty moves on board!", 500);
    }
}
