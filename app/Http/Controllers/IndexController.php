<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\PubSubService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IndexController extends Controller
{
    /**
     * @var PubSubService
     */
    public $pubSubService;

    /**
     * Create a new controller instance.
     *
     * @param PubSubService $pubSubService
     */
    public function __construct(PubSubService $pubSubService)
    {
        $this->pubSubService = $pubSubService;
    }
	
	/**
     * Returns all Active tasks
     * 
     * @return JsonResponse
     */
	 public function showAllActiveTasks(): JsonResponse
     {
        return $this->pubSubService->showAll();
     }
	 
	 /**
     * Returns Task By Id
     * 
     * @return JsonResponse
     */
	 public function getTask(): JsonResponse
     {
        return $this->pubSubService->getTask();
     }
	
	/**
     * Handles the registration of a subscriber
     * @param Request $request
     * @param string $topic
     *
     * @return JsonResponse
     */
    public function getActiveTasks()
	{
		$this->pubSubService->executeTasks();
		//return Tasks::where('status', '=', "New")->get();		
	}

    /**
     * Handles the registration of a subscriber
     * @param Request $request
     * @param string $topic
     *
     * @return JsonResponse
     */
    public function createTask(Request $request, string $task_type): JsonResponse
    {
        $content = $request->getContent();
		$subscriberUrl = [];

        if (! $content) {
            return response()->json(
                ["error" => "Empty request body", "status" => false], 400
            );
        }

        $content = json_decode($content, true);
        if (isset($content["url"])) {
            $subscriberUrl = $content["url"];
        }
        
        $data = $this->pubSubService->createTask($task_type, $subscriberUrl);

        return response()->json(["data" => $data, "status" => true],201);
    }

    /**
     * Handles publishing an event
     *
     * @param Request $request
     * @param string $topic
     * @throws \App\Exceptions\NoSubscriberFoundException
     * @return JsonResponse
     */
    public function publish(Request $request, string $topic): JsonResponse
    {
        $content = $request->getContent();
        $this->pubSubService->publish($topic, $content);

        return response()->json(["data" => "success", "status" => true],200);
    }

    /**
     * Prints out event data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function event(Request $request)
    {
        echo $request->getContent();
        return response()->json(["data" => "success", "status" => true], 200);
    }
}
