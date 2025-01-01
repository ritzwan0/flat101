<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\NoSubscriberFoundException;
use App\HttpClient;
use App\PubSubService;
use App\SubscriptionRepository;
use Tests\TestCase;
use Mockery as m;

class PubSubServiceTest extends TestCase
{
    /**
     * @var m\MockInterface|SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * @var m\MockInterface|HttpClient
     */
    protected $httpClient;

    public function setUp(): void
    {
        parent::setUp();

        $this->subscriptionRepository = m::mock("App\SubscriptionRepository");
        $this->httpClient = m::mock("App\HttpClient");
    }

    public function testSubscriptionSuccessfullyRegistered()
    {
        $topic = "notification";
        $url = "http://localhost:8000/event";

        $this->subscriptionRepository
            ->shouldReceive("addSubscription")
            ->with($topic, $url)
            ->once();

        $service = new PubSubService(
            $this->subscriptionRepository,
            $this->httpClient
        );

        $service->createTask($topic, $url);
    }

    public function testPublishingEventHavingNoSubscriberThrows()
    {
        $this->subscriptionRepository->shouldReceive("getSubscribers")
            ->once()
            ->andReturn([]);

        $service = new PubSubService(
            $this->subscriptionRepository,
            $this->httpClient
        );

        $this->expectException(NoSubscriberFoundException::class);

        $service->publish("topic1", json_encode([
            "message"=> "hello"
        ]));
    }

}
