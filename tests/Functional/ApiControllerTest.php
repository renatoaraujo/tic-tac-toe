<?php

namespace TicTacToe\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ApiControllerTest
 * @package TicTacToe\Tests\Functional
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class ApiControllerTest extends WebTestCase
{
    /**
     * @todo Implement the request body and validate the response
     */
    public function testMakeMove(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/move');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    /**
     * @todo
     */
    public function testMakeMoveBotWins(): void
    {
    }

    /**
     * @todo
     */
    public function testInvalidMakeMoveRequest(): void
    {
    }

    /**
     * @todo
     */
    public function testMakeMovePlayerWins(): void
    {
    }

    /**
     * @todo
     */
    public function testMakeMoveTiedGame(): void
    {
    }
}
