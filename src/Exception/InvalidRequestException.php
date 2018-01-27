<?php

namespace TicTacToe\Exception;

use Throwable;

/**
 * Class InvalidRequestException
 * @package TicTacToe\Exception
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class InvalidRequestException extends \Exception
{
    /**
     * InvalidRequestException constructor.
     *
     * @param Throwable|null $previous
     */
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Invalid or malformed request.", 400, $previous);
    }
}
