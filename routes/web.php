<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Lumen: Here is where you can register all of the routes for an application.
|
*/

Route::post("/subscribe/{topic}", "IndexController@createTask");

Route::post("/publish/{topic}", "IndexController@publish");

Route::post("/event", "IndexController@event");
