<?php

namespace App\Controller;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\HttpAsyncClient;
use Http\Message\RequestFactory;
use Http\Promise\Promise;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController
{
    private const URIS = [
        'http://httplug.io/',
        'https://conference.phpbenelux.eu/2019/',
        'https://nonexistent.domain.lo',
        'https://www.google.com/thisisnotfound',
    ];

    /**
     * @var HttpMethodsClient
     */
    private $httpClient;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    public function __construct(HttpAsyncClient $httpClient, RequestFactory $requestFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @Route("/dashboard")
     */
    public function dashboard(): Response
    {
        try {
            $promises = [];
            foreach (self::URIS as $uri) {
                $promises[$uri] = $this->httpClient->sendAsyncRequest($this->requestFactory->createRequest('GET', $uri));
            }
        } catch (\Exception $e) {
            return new Response($this->renderBody(
                'Configuration error',
                'Failed to check status: '.$e->getMessage()
            ), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $table = '<table><tr><th>URI</th><th>Status</th></tr>';
        /**
         * @var Promise $promise
         */
        foreach ($promises as $uri => $promise) {
            $table .= '<tr><td>'.$uri.'</td><td>';
            try {
                $response = $promise->wait();
                if (200 === $response->getStatusCode()) {
                    $table .= 'Website is up and running';
                } else {
                    $table .= 'Website responded with an error code: '.$response->getStatusCode();
                }
            } catch (\Exception $e) {
                $table .= 'Error while talking to server: '.$e->getMessage();
            }

            $table .= '</td></tr>';
        }
        $table .= '</table>';

        return new Response($this->renderBody('Dashboard', $table));
    }

    private function renderBody(string $title, string $msg): string
    {
        return sprintf(
            '<html><head><title>%s</title><style>table, th, td {border: 1px solid;} td {padding:4px;}</style></head><body>%s</body></html>',
            $title,
            $msg
        );
    }
}
