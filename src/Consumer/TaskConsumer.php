<?php

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Entry;

class TaskConsumer implements ConsumerInterface
{
    /**
     * ManagerRegistry $doctrine
     *
     * @since 0.1.0
     * @var ManagerRegistry
     */
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Creates new entry in database when one is available to be consumed
     *
     * @since 1.0.0
     * @param AMQPMessage $msg Message that was consumed.
     * @return void
     */
    public function execute(AMQPMessage $msg)
    {
        $entityManager = $this->doctrine->getManager();

        $entry = new Entry();
        $entry->setResponse($msg->body);
        $entry->setTimestamp(strtotime("now"));

        $entityManager->persist($entry);
        $entityManager->flush();
    }
}
