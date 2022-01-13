<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ProducerService;


class ApiController extends AbstractController
{

    /**
     * HttpClientInterface variable
     *
     * @since 1.0.0
     * @var HttpClientInterface
     */
    private $client;

    /**
     * $producerService variable
     *
     * @since 1.0.0
     * @var ProducerService
     */
    private $producerService;


    public function __construct(HttpClientInterface $client, ProducerService $producerService)
    {
        $this->client = $client;
        $this->producerService = $producerService;
    }

    /**
     * @Route("/")   
     * @Method({"GET"});
     */
    public function sendMessage()
    {
        // Create routing key
        $response = $this->client->request(
            'GET',
            'https://a831bqiv1d.execute-api.eu-west-1.amazonaws.com/dev/results'
        );

        $routing_key = array();
        $params = array('gatewayEui', 'profileId', 'endpointId', 'clusterId', 'attributeId');

        $responseAsArray = $response->toArray();

        // Create routing key
        foreach ($params as $key) {
            $routing_key[] = $this->bchexdec($responseAsArray[$key]);
        }

        $message['routing_key'] =  implode(".", $routing_key);
        $message['body'] = array(
            'value' => $responseAsArray['value'],
            'timestamp' => $responseAsArray['timestamp']
        );

        // Produce Message
        $this->producerService->publishMessage($message);
        return new Response('Sent');
    }

    /**
     * Converts hex to decimal. This is used instead of hexdec() because it can handle large numbers that overflow.
     *
     * @since 1.0.0
     * @param string $hex The hexadecimal string.
     * @return string $dec The hexadecimal converted to decimal.
     */
    private function bchexdec($hex)
    {
        $dec = 0;
        $len = strlen($hex);
        for ($i = 1; $i <= $len; $i++) {
            $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
        }
        return $dec;
    }
}
