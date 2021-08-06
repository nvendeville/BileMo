<?php

namespace App\EventListener;

use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $code = 500;
        $message = "Internal error.";

        switch (get_class($event->getThrowable())) {
            case NotFoundHttpException::class:
                $code = 404;
                $message = 'Aucune ressource trouvée';
                break;
            case NotNullConstraintViolationException::class:
                $code = 400;
                $message = 'Les données envoyées sont invalides';
                break;
            case AccessDeniedHttpException::class:
                $code = 403;
                $message = 'Vous n\'avez pas accès à cette fonction';
                break;
        }

        $event->setResponse(
            new JsonResponse(
                $this->serializer->serialize(
                    ['code' => $code, 'message' => $message],
                    'json'
                ),
                $code,
                ['Content-Type' => 'application/problem+json'],
                true
            )
        );
    }

    #[ArrayShape([KernelEvents::EXCEPTION => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }
}
