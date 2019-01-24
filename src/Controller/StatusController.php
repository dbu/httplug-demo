<?php

namespace App\Controller;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatusController
{
    /**
     * @var HttpMethodsClient
     */
    private $httpClient;

    public function __construct(HttpMethodsClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @Route("/")
     */
    public function status(): Response
    {
        try {
            $response = $this->httpClient->get('https://conference.phpbenelux.eu/2019/');
        } catch (Exception $e) {
            return new Response($this->renderBody(
                'Network error',
                'Failed to contact the conference website: '.$e->getMessage()
            ), Response::HTTP_BAD_GATEWAY);
        }

        if (200 === $response->getStatusCode()) {
            return new Response($this->renderBody('Success', 'Conference website is up and running'));
        }

        return new Response($this->renderBody(
            'Error Response',
            'Conference website responded with an error code: '.$response->getStatusCode()
        ), Response::HTTP_BAD_GATEWAY);
    }

    private function renderBody(string $title, string $msg): string
    {
        return sprintf(
            '<html><head><title>%s</title></head><body>%s</body></html>',
            $title,
            $msg
        );
    }
}
