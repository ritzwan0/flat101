<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\HttpClient;
use Tests\TestCase;
use Mockery as m;

class PubSubTest extends TestCase
{
    /**
     * @var m\MockInterface
     */
    protected $httpClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->httpClient = m::mock(HttpClient::class);
    }


    public function testSubscriptionIsSuccessfullyRegistered()
    {
        $response = $this->json("POST", "/subscribe/topic1", [
            "url" => "http://localhost:9090/event"
        ]);

        $response->seeStatusCode(201)
            ->seeJson(["status" =>  true]);
    }

    public function testEventIsSuccessfullyPublished()
    {
        // Initial request to register a subscription
        $this->json("POST", "/subscribe/topic1", [
            "url" => "http://localhost:9090/event"
        ]);

        $this->httpClient->shouldReceive("concurrentRequest")
            ->once();

        app()->instance(HttpClient::class, $this->httpClient);

        $response = $this->json("POST", "/publish/topic1", [
            "message" => "hello"
        ]);

        $response->seeStatusCode(200)
            ->seeJson(["status" => true]);
    }
}
