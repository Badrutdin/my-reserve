<?php

// src/EventListener/ExceptionListener.php
namespace App\EventListener;

use App\Util\JsonResponseHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use TypeError;

#[AsEventListener]
final class ExceptionListener
{

    // Внедряем Logger через конструктор
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {

        $exception = $event->getThrowable();
        $message = $exception->getMessage();

        $statusCode = $exception->getCode() > 0 ? $exception->getCode() : 500;
        if ($exception instanceof TypeError) {
            $statusCode = 500; // Или 500, если ошибка критическая
            $this->logger->error($message);
            $message = 'Something went wrong';
        }

        if ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $this->logger->error($message);

        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            $statusCode = 405;
            $this->logger->error($message);
            $message = json_encode([
                'error' => 'Method Not Allowed',
                'allowed_methods' => $exception->getHeaders()['Allow'] ?? []
            ], true);

        }
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        $oldMessage = $message;
        if (json_last_error() === JSON_ERROR_NONE) $message = json_decode($message, true);
        if (gettype($message) == 'NULL') $message = $oldMessage;
        $response = JsonResponseHelper::error($message, $statusCode);
        $event->setResponse($response);
    }
}