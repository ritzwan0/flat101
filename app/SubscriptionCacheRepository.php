<?php

declare(strict_types=1);

namespace App;

use Illuminate\Support\Facades\Cache;

final class SubscriptionCacheRepository implements SubscriptionRepository
{

    const SUBSCRIPTIONS = "subscriptions";
	
	public function getTasks()
	{		
		$tasks = Cache::remember(static::SUBSCRIPTIONS, 30, function() {
			return Tasks::where('status', '=', "New")->orderBy('priority', 'DESC')->get();
		});
		return $tasks;
			
	}
	
	public function getTaskById($id)
	{		
		$tasks = Cache::remember(static::SUBSCRIPTIONS, 30, function() {
			return Tasks::get($id);
		});
		return $tasks;
			
	}
	
	public function updateTasks($task_type, $url, $status)
	{
		$tasks = Tasks::where('task_type', $task_type)
			->where('url', $url)
            ->update(['status' => $status]);
		Cache::flush();
			
	}
	

    public function addSubscription(
        string $task_type,
        string $url,
		string $status,
		string $priority
    ): void
    {
        $subscriptions = Cache::get(static::SUBSCRIPTIONS);

        if (!$subscriptions) {
            $subscriptions = [];
        }

        $subscriptions[$task_type][$url] = true;

        Cache::put(static::SUBSCRIPTIONS, $subscriptions);
    }

    public function getSubscribers(string $task_type, string $url, string $status, string $priority ): array
    {
        $subscriptions = Cache::get(static::SUBSCRIPTIONS);

        if (!$subscriptions || !isset($subscriptions[$task_type][$url])) {
            return [];
        }

        return array_keys($subscriptions[$task_type][$url]);
    }
	
	
}
