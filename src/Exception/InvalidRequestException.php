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
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 412, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
