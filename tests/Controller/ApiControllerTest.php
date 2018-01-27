<?php

namespace TicTacToe\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ApiControllerTest
 * @package TicTacToe\Tests\Controller
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class ApiControllerTest extends WebTestCase
{
    public function testMakeMove(): void
    {
        $requestContent = json_encode([
            "playerUnit" => "X",
            "boardState" => [
                ["X", "O", ""],
                ["X", "O", "O"],
                ["O", "X", "X"]
            ]
        ]);
        $client = static::createClient();
        $client->request('POST', '/api/move', [], [], [], $requestContent);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $contentBody = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('nextMove', $contentBody);
    }
}
