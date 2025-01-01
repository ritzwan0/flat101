<?php

use Illuminate\Database\Seeder;

class TasksSeeder extends Seeder
{
	
	CONST
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        // Create 100 tasks in our database:
        for ($i = 0; $i < 100; $i++) {
			$taskId = rand(1,3);
			$taskType = $this->getTaskType($taskId);
			$priority = ($taskId == 1 ? '1' : '0');	// Assign fetchUrl higher priority
            Tasks::create([
				'task_type' => $taskType,
                'url' => $faker->url,
                'status' => "New",		// Initial status is 'New'
				'priority' => $priority
            ]);
        }
    }
	
	public function getTaskType($taskId)
	{
		switch ($taskId) {
		  case "1":
			return "fetchUrlTitle";
			break;
		  case "2":
			return "callApi";
			break;
		  case "3":
			return "sleep";
			break;		  
		}
		
	}
}
