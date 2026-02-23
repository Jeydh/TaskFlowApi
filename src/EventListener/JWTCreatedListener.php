<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Uid\Uuid;

final class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $payload['jti'] = Uuid::v4()->toRfc4122(); // Generate a unique identifier for the token
        $event->setData($payload);
    }
}