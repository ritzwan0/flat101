<?php

declare(strict_types=1);

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;

class HttpClient
{
    public function concurrentRequest(
        string $method,
        string $data,
        array $endpoints)
    {
        $client = new Client();

        $headers = [
            "Content-Type" => "application/json"
        ];

        $requests = function (
            string $method,
            array $endpoints,
            array $headers,
            string $data) {

            foreach ($endpoints as $uri) {
                yield new Request($method, $uri, $headers, $data);
            }
        };

        $pool = new Pool($client, $requests($method, $endpoints, $headers, $data), [
            'concurrency' => 10,
            'fulfilled' => function (Response $response, $index) {
                Log::info($response->getBody()->getContents());
            },
            'rejected' => function (RequestException $reason, $index) {
                Log::error($reason->getMessage());
            },
            'options' => [
                "timeout" => 10
            ]
        ]);

        // Initiate the transfers and create a promise
        $promise = $pool->promise();

        // Force the pool of requests to complete.
        $promise->wait();
    }
}
