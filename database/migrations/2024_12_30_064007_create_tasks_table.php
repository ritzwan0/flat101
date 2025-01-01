<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
			$table->increments('id'); // Creates an auto-incrementing ID
			$table->string('task_type'); // Create a string Task type column
			$table->string('url'); // Create a string URL column
			$table->string('status'); // Create a string Status column
			$table->string('result'); // Create a string Result column
			$table->integer('priority'); // Creates an integer priority level column
			$table->timestamps(); // Creates created_at and updated_at columns
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}