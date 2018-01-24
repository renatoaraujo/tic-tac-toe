<?php

namespace TicTacToe\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
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

    public function testMakeMoveBotWins(): void
    {}

    public function testInvalidMakeMoveRequest(): void
    {}

    public function testMakeMovePlayerWins(): void
    {}

    public function testMakeMoveTiedGame(): void
    {}
}