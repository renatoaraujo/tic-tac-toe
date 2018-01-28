<?php

namespace TicTacToe\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TicTacToe\Exception\InvalidRequestException;
use TicTacToe\Util\Validator\ApiRequestValidator;

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
        if (!ApiRequestValidator::isValid($request->getContent())) {
            throw new InvalidRequestException();
        }
        $game = $this->get('TicTacToe\Service\GameService')->createGame($request->getContent());
        return $this->json($game);
    }
}
