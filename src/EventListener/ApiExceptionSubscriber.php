<?php

namespace App\EventListener;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
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
        switch (get_class($event->getThrowable())) {
            case NotFoundHttpException::class:
                $event->setResponse(
                    new JsonResponse(
                        $this->serializer->serialize(
                            ['code' => 404, 'message' => 'Aucune ressource trouvée'],
                            'json'
                        ),
                        404,
                        ['Content-Type' => 'application/problem+json'],
                        true
                    )
                );
                break;
            case NotEncodableValueException::class:
                $event->setResponse(
                    new JsonResponse(
                        $this->serializer->serialize(
                            ['code' => 400, 'message' => 'Les données envoyées sont invalides'],
                            'json'
                        ),
                        400,
                        ['Content-Type' => 'application/problem+json'],
                        true
                    )
                );
                break;
            case AccessDeniedHttpException::class:
                $event->setResponse(
                    new JsonResponse(
                        $this->serializer->serialize(
                            ['code' => 403, 'message' => 'Vous n\'avez pas accès à cette fonction'],
                            'json'
                        ),
                        403,
                        ['Content-Type' => 'application/problem+json'],
                        true
                    )
                );
                break;
            default:
                $event->setResponse(
                    new JsonResponse(
                        $this->serializer->serialize(
                            ['code' => 500, 'msg' => 'Internal error.'],
                            'json'
                        ),
                        500,
                        ['Content-Type' => 'application/problem+json'],
                        true
                    )
                );
        }
    }

    #[ArrayShape([KernelEvents::EXCEPTION => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }
}
