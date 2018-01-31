<?php

namespace TicTacToe\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TicTacToe\Entity\Game;
use TicTacToe\Exception\InvalidRequestException;
use TicTacToe\Util\GameStatus;
use TicTacToe\Util\GameUnit;
use TicTacToe\Util\Validator\ApiRequestValidator;
use TicTacToe\Util\WinnerMoves;

/**
 * Class ApiController
 * @package TicTacToe\Controller
 * @Route("/api")
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class ApiController extends Controller
{
    /**
     * @Route("/move", defaults={"_format": "json"})
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function moveAction(Request $request): JsonResponse
    {
        try {
            $content = $request->getContent();

            $validator = new ApiRequestValidator();
            $validator->isValid($content);

            $requestContent = json_decode($content);
            $nextMove = $this->get('TicTacToe\Service\MoveService')
                ->makeMove($requestContent->boardState, $requestContent->playerUnit);

            return $this->createResponse($requestContent->boardState, $requestContent->playerUnit, $nextMove);
        } catch (InvalidRequestException $exception) {
            throw $exception;
        }
    }

    /**
     * @param array $boardState
     * @param string $playerUnit
     * @param array $nextMove
     *
     * @return JsonResponse
     */
    private function createResponse(array $boardState, string $playerUnit, array $nextMove)
    {
        $response = [
            'playerUnit' => $playerUnit,
            'boardState' => $boardState,
            'nextMove' => $nextMove,
        ];

        $gameStatus = new GameStatus($boardState, $playerUnit, $nextMove);

        if ($gameStatus->isTied()) {
            $response['tied'] = $gameStatus->isTied();
        } elseif (!empty($gameStatus->getWinner())) {
            $response['winner'] = $gameStatus->getWinner();
        }

        return new JsonResponse($response, 200);
    }
}
