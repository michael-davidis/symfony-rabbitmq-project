<?php

namespace App\Service;

require_once(__DIR__ . '/../../vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Service\DatabaseService;

class MessengerService
{

    public function getMessage() {
        //Connect as receiver
        $connection = new AMQPStreamConnection('candidatemq.n2g-dev.net', 5672, 'cand_ygga', 'yLzXlNnywVJrpz5G');
        $channel = $connection->channel();
        $channel->queue_declare('cand_ygga_results', false, false, false, false);

        // Callback to be executed when we receive a message.
        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
            // DatabaseService::insertEntity();
        };

        $channel->basic_consume('cand_ygga_results', '', false, true, false, false, $callback);
    }

    /**
     * Send message to queue.
     *
     * @since 1.0.0
     * @param string $message Message to be sent.
     * @return void
     */
    public function sendMessage($message) {
        // Connect to channel as publisher
        $connection = new AMQPStreamConnection('candidatemq.n2g-dev.net', 5672, 'cand_ygga', 'yLzXlNnywVJrpz5G');
        $channel = $connection->channel();
        $channel->exchange_declare('cand_ygga', 'fanout');

        // Send the actual message
        $channel->basic_publish(new AMQPMessage($message['body']), '', $message['routing_key']);
        $connection->close();
    }
}
