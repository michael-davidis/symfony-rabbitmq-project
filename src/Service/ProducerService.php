<?php

namespace App\Service;


use App\Rabbit\MessagingProducer;
use Symfony\Component\HttpFoundation\JsonResponse;
use PhpAmqpLib\Message\AMQPMessage;

class ProducerService
{
    /**
     * MessagingProducer $messagingProducer
     *
     * @since 0.1.0
     * @var MessagingProducer
     */
    private $messagingProducer;

    public function __construct(MessagingProducer $messagingProducer)
    {
        $this->messagingProducer = $messagingProducer;
    }

    /**
     * Publishes message to queue
     *
     * @since 1.0.0
     * @param string[] $message Message to published.
     * @return JsonResponse
     */
    public function publishMessage($message): JsonResponse
    {
        $this->messagingProducer->publish(implode($message['body']),  $message['routing_key'], [
            'content_type'  => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        return new JsonResponse(['status' => 'Sent!']);
    }
}
