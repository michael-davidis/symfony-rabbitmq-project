<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\MessengerService;


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
     * MessengerService variable
     *
     * @since 1.0.0
     * @var MessengerService
     */
    private $messengerService;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->messengerService = new MessengerService();
        $this->messengerService->getMessage();
    }

    /**
     * @Route("/")   
     * @Method({"GET"});
     */
    public function consume()
    {
        $response = $this->client->request(
            'GET',
            'https://a831bqiv1d.execute-api.eu-west-1.amazonaws.com/dev/results'
        );

        $routing_key = array();
        $params = array('gatewayEui', 'profileId', 'endpointId', 'clusterId', 'attributeId');

        $responseAsArray = $response->toArray();

        foreach ($params as $key) {
            $routing_key[] = $this->bchexdec($responseAsArray[$key]);
        }

        $message = array();
        $message['routing_key'] =  implode(".", $routing_key);
        $message['body'] = array( 
            'value' => $responseAsArray['value'],
            'timestamp' => $responseAsArray['timestamp']
        );

        $this->messengerService->sendMessage( $message );

        return new Response( $message['routing_key'] . '<br>' . implode( '|', $message['body'] ) );
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
