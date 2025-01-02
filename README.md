# PubSub

This is an implementation of a publisher/subscriber system using http requests. 

Lumen (a micro-framework by Laravel) is used as the development framework.

## Readme Notes
* {project root directory} is a placeholder for the root directory of this project

## Setup

* `$ cd {project root directory} # change directory to project root` 
* `$ composer install # install project dependencies`

## Project Structure
* Core project code can be found in the app directory relative to the project's root directory
* Unit and integration tests can be found in tests/Unit and tests/Feature directories both relative to the project's root directory 

## Run Application
Project can be run (in development mode) in one of two ways

### From start-up script
* `$ cd {project root directory} # change directory to project root` 
* `$ chmod 744 start-server.sh # add permission to execute script`
* `$ ./start-server.sh # server will be listening on port 8000`

### With built-in php server
* `$ cd {project root directory}/public # navigate to public folder`
* `$ php -S localhost:8000 # run built-in php server listen on port 8000`

## Create table
php artisan make:migration create_tasks_table

## Edit the Migration File
Open the newly created migration file located in the database/migrations directory. It will have a timestamp in the filename. In the up method, define the columns for your table. For example:

//TasksSeeder
* use Illuminate\Database\Migrations\Migration;
* use Illuminate\Database\Schema\Blueprint;
* use Illuminate\Support\Facades\Schema;
* class TasksSeeder extends Migration
* {
* public function up()
{
	Schema::create('tasks', function (Blueprint $table) {
		$table->increments('id'); // Creates an auto-incrementing ID
		$table->string('url'); // Create a string URL column
		$table->string('status'); // Create a string Status column
		$table->string('result'); // Create a string Result column
		$table->integer('priority'); // Creates an integer priority level column
		$table->timestamps(); // Creates created_at and updated_at columns
	});
}

public function down() 
{ 
    Schema::dropIfExists('tasks'); 
}

## Run the Migration to create DB table
After defining your table structure, run the migration to create the table in the database:

bash 
php artisan migrate 

## Add 100 tasks using Fakefactory:

Run the command to create seeder file in database/seeders -
php artisan make:seeder TasksSeeder

Run the seed -
php artisan db:seed -–class=TasksSeeder

## Event Listners
Event Listners are configured to observe any changes in DB. The event is created in app/Events. Listners are added in app/Listners. Any changes in data wll trigger the executeTasks method.

## API Methods
Following APIs are defined in routes/api.php
•	Route::get('tasks/all', 'Controller@showAllActiveTasks');		// List all Active Tasks
•	Route::get('tasks/{id}', 'Controller@getTask');	// Get Task details with Task ID
•	Route::post('tasks/create', 'Controller@createTask');		// Create a new Task


## Swagger file

API swagger files are in swagger folder

## Methods used

Following Methods execute the tasks:

showAllActiveTasks() // Returns all active tasks in JSON format
getTask(int $id)  // Returns Task by its ID
executeActiveTasks()    // Executes all Active Tasks
createTask(Request $request, string $task_type)    // Creates a New Task

There are 3 types of tasks-
fetchUrlTitle
callAPI
sleep
These tasks are retried 3 times with 30 seconds delay between each retry using method retry($f, $args = null, $delay = 30, $retries = 3)


## Dataase caching

Illuminate Caching mechanism is used to cache records form DB to optimize the performance. The Caching methods are defined in SubscriptionCacheRepository. The cahcing time is set to 30 mins, after which is is refreshes form DB updated records.

getTasks() // Returns from teh cache all tasks with status as "New" sorted with priority order.
getTaskById($id)    // Returns from the cache the task by its ID. 
updateTasks($task_type, $url, $status)    // Updates cache record of the task and flushes it.
addSubscription(
        string $task_type,
        string $url,
		string $status,
		string $priority
    )        // Add a task to DB cache

## Things to be added
User Authentication: GIvne the scope of task, the user authentication needs to be added

## Testing

### Run all tests (unit and integration)
* `$ cd {project root directory} # change directory to project root` 
* `$ ./vendor/bin/phpunit tests`

### Run only unit tests
* `$ cd {project root directory} # change directory to project root` 
* `$ ./vendor/bin/phpunit tests/Unit`

### Run only integration tests
* `$ cd {project root directory} # change directory to project root` 
* `$ ./vendor/bin/phpunit tests/Feature`




