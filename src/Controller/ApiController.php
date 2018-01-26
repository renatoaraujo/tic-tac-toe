<?php

namespace TicTacToe\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/move")
     * @Method("POST")
     *
     * @return JsonResponse
     */
    public function moveAction(Request $request): JsonResponse
    {
        $game = $this->get('TicTacToe\Service\GameService')
            ->createGame($request->getContent());
        return $this->json($game);
    }
}
