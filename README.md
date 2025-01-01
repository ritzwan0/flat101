# PubSub

This is an implementation of a publisher/subscriber system using http requests. 

Lumen (a very fast PHP micro-framework) was used as the development framework.

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

## Run Tests
As a prerequisite, ensure all project dependencies have been installed as specified under the **Setup** section

### Run all tests (unit and integration)
* `$ cd {project root directory} # change directory to project root` 
* `$ ./vendor/bin/phpunit tests`

### Run only unit tests
* `$ cd {project root directory} # change directory to project root` 
* `$ ./vendor/bin/phpunit tests/Unit`

### Run only integration tests
* `$ cd {project root directory} # change directory to project root` 
* `$ ./vendor/bin/phpunit tests/Feature`




