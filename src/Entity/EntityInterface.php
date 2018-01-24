<?php

namespace TicTacToe\Entity;

/**
 * Interface EntityInterface
 * @package TicTacToe\Entity
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
interface EntityInterface
{
    /**
     * Return entity object as array
     *
     * @return array
     */
    public function toArray(): array;
}