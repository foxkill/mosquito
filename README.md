<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


# Mosquito Task Management API

Mosquito is a lightweight task management API designed to allow users to create, update, delete, and list tasks. It provides a simple and efficient way to manage tasks in a multi-user environment.

## Installation

To use Mosquito API, follow these steps:

1. Clone the repository to your local machine:

```
git clone https://github.com/foxkill/mosquito.git
```

2. Navigate to the project directory:

```
cd mosquito
```

3. Install dependencies using Composer:

```
composer install
```

4. Set up your environment by using my separately provided .env

```
cp .env  mosquito/
```

```

5. Run the database migrations to create the necessary tables:

```
php artisan migrate
```

7. Serve the application:

```
php artisan serve
```

The API should now be available at `http://localhost`

## Usage

### Authentication

To access the API endpoints, you must authenticate yourself using JWT (JSON Web Tokens). Send a POST request to the 
`/api/login` endpoint with your email and password to receive an access token.

Example Request:

```
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "secret"
}
```

Example Response:

```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0a...",
    "token_type": "Bearer",
    "expires_in": 3600
}
```

Include the access token in the Authorization header of subsequent requests to authenticate yourself.

### Endpoints

- **GET /api/v1/tasks**: Retrieve all tasks belonging to the authenticated user.
- **POST /api/v1/tasks**: Create a new task.
- **GET /api/tasks/v1/{id}**: Retrieve a specific task.
- **PUT /api/tasks/v1/{id}**: Update a task.
- **DELETE /api/tasks/v1/{id}**: Delete a task.

## Testing

This project includes PHPUnit tests to ensure the correctness of the API functionality. To run the tests, use the following command:

```
sail artisan test
```

## Contributors

- Stefan Martin

## License

This project is licensed under the MIT License - see the License file for details.

### Preparations
- add alias: alias sail=vendor/bin/sail to make life easier :)
- change CACHE_STORAGE in .env to redis
- change engine entry in config/database.php to use ROW_FORMAT=dynamic
- enable debugging:
- add SAIL_XDEBUG_MODE=develop,debug in .env
- add SAIL_XDEBUG_CONFIG="client_host=localhost" in .env
- as Laravel 11.0 has no api route file, create it: sail artisan install:api (which installs sanctum too),
  allow to run the migrations too.
- check if the engine command use ROW_FORMAT=dynamic
- sail artisan mysql -h mysql
- show create table personal_access_tokens\G
*************************** 1. row ***************************
       Table: personal_access_tokens
Create Table: CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  ...
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC
- looks good.
- sail artisan make:migration create_tasks_table
- sail artisan make:model Task -f (i should have used -mf, but ok)
- get the extensions currently used: code --list-extensions
- added these vscode extension in devcontainer: 
  "bmewburn.vscode-intelephense-client" -> intellisense
  "xdebug.php-debug" - debugging
  "rangav.vscode-thunder-client" - api client
- make the controller: sail artisan make:controller Api/V1/TaskController --api
- add the routes to api.php
- check the correctness of the routes: sail artisan route:list
- add the Sanctum EnsureFrontendRequestsAreStateful Middleware by adding $middleware->statefulApi() all to the bootstrap file.
- Start to create tests and then the implementation of the controller:  sail artisan make:test Api/V1/TaskTest
- Make StoreTaskRequest to validate the user input in the store request: sail artisan make:request V1/StoreTaskRequest
- Add class StateEnum for validating states.
- Add HasApiTokens to the User Model class.
- Add a scope to make sure users have access only to their own tasks: sail artisan make:scope CreatorScope
- sail artisan make:test Api/V1/TaskAuthTest for testing the sanctum abilites.
- Make a resource to deliver only needed fields: sail artisan make:resource V1/TaskResource
- TOD0: 
    * title=max:255 
    * Json-Middleware
    * better provision of tokens.
    * OpenAPI provision








