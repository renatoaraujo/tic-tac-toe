<?php

namespace TicTacToe\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IndexController
 * @package TicTacToe\Controller
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class IndexController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Method("GET")
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('index/index.html.twig');
    }
}
