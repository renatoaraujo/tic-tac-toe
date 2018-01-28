<?php

namespace TicTacToe\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ExceptionSubscriber
 * @package TicTacToe\EventListener
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 */
class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        $exceptionData = [
            'message' => $exception->getMessage(),
        ];

        $statusCode = ($exception->getCode() == 0) ? 500 : $exception->getCode();

        $response = new JsonResponse($exceptionData, $statusCode);
        $event->setResponse($response);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
