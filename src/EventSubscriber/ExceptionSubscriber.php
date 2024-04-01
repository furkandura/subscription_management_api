<?php

namespace App\EventSubscriber;

use App\Dto\GenericResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    const VALIDATION_ERROR_KEY = 'VALIDATION_ERROR';
    public function __construct()
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent'
        ];
    }

    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $previousException = $event->getThrowable()->getPrevious();

        if ($exception instanceof HttpExceptionInterface) {
            if ($previousException instanceof ValidationFailedException) {
                $this->handleValidationException($event, $previousException);
            } else {
                $this->handleHttpException($event, $exception);
            }
        } else {
            $this->handleGenericException($event, $exception);
        }
    }

    private function handleValidationException(ExceptionEvent $event, ValidationFailedException $exception): void
    {
        $responseDto = (new GenericResponseDto())
            ->setCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->setMessage(self::VALIDATION_ERROR_KEY)
        ;

        $errors = [];
        foreach ($exception->getViolations() as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        if (count($errors)) {
            $responseDto->setErrors($errors);
        }

        /*if ($this->isDebug) {
            $responseDto->setTrace($exception->getTrace());
        }*/

        $this->setJsonResponse($event, $responseDto->toArray(), $responseDto->getCode());
    }

    private function handleHttpException(ExceptionEvent $event, HttpExceptionInterface $exception): void
    {
        $responseDto = (new GenericResponseDto())
            ->setCode($exception->getStatusCode())
            ->setMessage($exception->getMessage())
        ;

        /*if ($this->isDebug) {
            $responseDto->setTrace($exception->getTrace());
        }*/

        $this->setJsonResponse($event, $responseDto->toArray(), $responseDto->getCode());
    }

    private function handleGenericException(ExceptionEvent $event, \Throwable $exception): void
    {
        $responseDto = (new GenericResponseDto())
            ->setCode(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->setMessage($exception->getMessage())
        ;

        /*if ($this->isDebug) {
            $responseDto->setTrace($exception->getTrace());
        }*/

        $this->setJsonResponse($event, $responseDto->toArray(), $responseDto->getCode());
    }

    private function setJsonResponse(ExceptionEvent $event, array $content, int $statusCode): void
    {
        $event->setResponse(new JsonResponse($content, $statusCode));
    }
}
