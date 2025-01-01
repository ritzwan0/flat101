<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

Route::group(['controller' => 'auth:api'], function() {

	Route::get('tasks/all', 'Controller@showAllActiveTasks');		// List all Active Tasks
	Route::get('tasks/{id}', 'Controller@getTask');	// Get Task details with Task ID
	Route::post('tasks/create', 'Controller@createTask');		// Create a new Task
	

});
