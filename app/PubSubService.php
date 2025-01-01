<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\NoSubscriberFoundException;
use Illuminate\Database\Capsule\Manager as Connection;

class PubSubService
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        HttpClient $httpClient)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->httpClient = $httpClient;
    }
	
	
	
	/**
     * Get all Active Tasks from DB cache
     *
     *
     * @return string
     */
    public function showAll(): string
    {
		$result = $this->SubscriptionRepository->getTasks();
		return json_decode($result);
	}
	
	/**
     * Get all Active Tasks by Id
     *
     *
     * @return string
     */
    public function getTask(int $id): string
    {
		$result = $this->SubscriptionRepository->getTaskById($id);
		return json_decode($result);
	}

    /**
     * Orchestrates a task in DB
     *
     * @param string $topic
     * @param string $url
     *
     * @return array
     */
    public function createTask(string $task_type, string $url): array
    {
		$priority = ($task_type == "fetchUrl" ? '1' : '0'); // Assign fetchUrl higher priority
		$status = "New";	// Initial status is 'New'
        $this->subscriptionRepository
            ->addSubscription($task_type, $url, $status, $priority);

        return [
            "task_type" => $task_type,
            "url" => $url,
			'status' => "New",	
			"priority" => $priority
        ];
    }
	
	/**
     * Orchestrates publishing an event
     *
     * @param string $topic
     * @param string $content
     * @throws NoSubscriberFoundException
     */
    public function executeTasks()
	{
		$tasks = $this->SubscriptionRepository->getTasks();
		
		if (empty($tasks)) {
            throw new NoSubscriberFoundException(
                "No pending tasks found");
        }
		foreach($tasks as $task)
		{
			$status = "Completed";
			if($task->task_type == "fetchUrl")
			{
				$args[] = $task->url;
				$title = $this->retry('fetchUrlTitle', $args, $delay = 30, $retries = 3);
				if(!$title){
					$status = "Failed";
				}
				$this->SubscriptionRepository->updateTasks($task->task_type, $task->url, $status);
				
			}
			if($task->task_type == "callApi")
			{
				$args[] = $task->url;
				$result = $this->retry('callApi', $args, $delay = 30, $retries = 3);
				if(!$result){
					$status = "Failed";
				}
				$this->SubscriptionRepository->updateTasks($task->task_type, $task->url, $status);
			}
			if($task->task_type == "sleep")
			{				
				$title = $this->retry('callApi', $args = null, $delay = 30, $retries = 3);
				$this->SubscriptionRepository->updateTasks($task->task_type, $task->url, $status);
			}
			
		}
		
		
	}

    /**
     * Orchestrates publishing an event
     *
     * @param string $topic
     * @param string $content
     * @throws NoSubscriberFoundException
     */
    public function publish(string $task_type, string $url, string $status = "New", string priority)
    {
        $subscribers = $this->subscriptionRepository
            ->getSubscribers($task_type, $url, $status, "1");


        if (empty($subscribers)) {
            throw new NoSubscriberFoundException(
                "No pending tasks found for the url {$url}");
        }

        $data = json_encode(
            [
                "task_type" => $topic,
                "url" => $url,
				"status" => $status,
				"priority" => "1"
            ]
        );

        // Process the tasks with priority 1
        $this->httpClient->concurrentRequest(
            "POST",
            $data,
            $subscribers
        );
    }
	
	/**
     * Execute fetch Url task
     * @param string $url
     *  
     */
    public function fetchUrlTitle(string $url): string
	{
		
		// Extract HTML using curl 
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
		 
		$data = curl_exec($ch); 
		curl_close($ch); 
		 
		// Load HTML to DOM object 
		$dom = new DOMDocument(); 
		@$dom->loadHTML($data); 
		 
		// Parse DOM to get Title data 
		$nodes = $dom->getElementsByTagName('title'); 
		$title = $nodes->item(0)->nodeValue;
		return $title;		
		
	}
	
	/**
     * Execute fetch Url task
     * @param string $url
     *  
     */
    public function callApi(string $url): string
	{
		// Call REST GET API using curl
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, "Authorization: Bearer " . $access_token);
		curl_setopt($ch, CURLOPT_URL, $url);		
		$curl_response = curl_exec($curl);
		if ($curl_response === false) {
			$info = curl_getinfo($curl);
			curl_close($curl);
			die('error occured during curl exec. Additioanl info: ' . var_export($info));
		}
		curl_close($curl);
		$response = json_decode($curl_response);
		if (isset($response->response->status) && $response->response->status == 'ERROR') {
			die('error occured: ' . $response->response->errormessage);
		}		
		
	}
	
	/**
     * Sleep function
     * 
     *  
     */
    public function callSleep(): void
	{
		sleep(5);
		// rest of the task not understandable 
		
	}
	
	/** 
	*	Retries $f (A function) with arguments ($args as array)
	* 	3 ($retries) times after 10 secs $delay
	* 	Usage: 
	*   retry( 'function_name', array('arg1', 'arg2'), 15, 5 );
	*   #=> Retries function_name 3 times with arg1 and $arg2 as arguments at interval of 30 secs 
	**/	
	function retry($f, $args = null, $delay = 30, $retries = 3)
	{
		try {
			if (is_null($args))
				return $f();
			if (is_array($args))
			{
				return call_user_func_array($f, $args);
			}
			return call_user_func_array($f, array($args));
		} catch (Exception $e) {
			if ( $retries > 0) {
				sleep($delay);
				return retry($f, $delay, $retries - 1);
			} else {
				throw $e;
			}
		}
	}
		
	
}
