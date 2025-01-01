<?php

namespace App\Listeners;

use App\Events\TaskHandleEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers;

class TaskHandleListener
{
    private $taskListner;
	
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Controller $taskListner)
    {
        $this->taskListner = $taskListner;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\TaskHandleEvent  $event
     * @return void
     */
    public function handle(TaskHandleEvent $event)
    {
        $this->taskListner->getActiveTasks();
    }
}
