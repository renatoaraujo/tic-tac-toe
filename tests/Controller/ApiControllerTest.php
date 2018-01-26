<?php

namespace TicTacToe\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

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
        $client->request(
            Request::METHOD_POST,
            '/api/move',
            [],
            [],
            [],
            $requestContent);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }
}
