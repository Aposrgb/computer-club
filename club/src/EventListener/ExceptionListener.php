<?php

namespace App\EventListener;

use App\Helper\Exception\ApiException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class ExceptionListener
{
    private LoggerInterface $logger;

    protected string $environment;

    public function __construct(LoggerInterface $logger, KernelInterface $kernel)
    {
        $this->logger = $logger;
        $this->environment = $kernel->getEnvironment();
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $this->logger->error($exception->getMessage(), $exception->getTrace());
        if ($exception instanceof ApiException) {
            $errorJsonResponse = new JsonResponse($exception->responseBody()['error'], $exception->getStatusCode());
        } elseif ($exception instanceof HttpException) {
            $ex = new ApiException(message: $exception->getMessage(), status: $exception->getStatusCode());
            $errorJsonResponse = new JsonResponse($ex->responseBody()['error'], $ex->getStatusCode());
        } else if ($exception instanceof UnexpectedValueException) {
            $ex = new ApiException(message: $exception->getMessage(), status: Response::HTTP_BAD_REQUEST);
            $errorJsonResponse = new JsonResponse($ex->responseBody()['error'], $ex->getStatusCode());
        } else {
            return;
        }
        $event->setResponse($errorJsonResponse);
    }
}
