<?php

declare(strict_types=1);

namespace App;

interface SubscriptionRepository
{
    /**
     * Registers a subscription
     *
     * @param string $task_type
     * @param string $url
     */
    public function addSubscription(string $task_type, string $url, string $status, string $priority): void;

    /**
     * Returns a collection of all subscribers to a topic
     *
     * @param string $topic
     * @return array
     */
    public function getSubscribers(string $task_type, string $url): array;
	
	
   
}
