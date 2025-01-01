<?php

namespace App\Events;

use App\Http\Controllers;

class TaskHandleEvent extends Event
{
	
	private $activetasks;
	
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Controller $activetasks)
    {
        $this->activetasks = $activetasks;
    }
	
	
	/**
     * Create event to check data Updates
     *
     * @return \App\Http\Controllers  $activetasks
     */
    public function checkTasks(): array
    {
        return $this->activetasks->createTask(Request $request, string $task_type);
    }
}
