<p align="center"><img src="https://github.com/foxkill/mosquito/assets/7531860/19a06321-8566-4ae9-ae21-6b4e177f1663" width="300" heigh="300" /></p>

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

5. Install and setup Laravel Sail

```
vendor/bin/sail build --no-cache
```

7. Start Laravel Sail with

```
vendor/bin/sail up -d
```


8. Run the database migrations to create the necessary tables:

```
vendor/bin/sail artisan migrate
```

9. Import the routes in Postman

You'll find the routes in: 

```
{project_root}/public/docs/collection.json
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
    "email": "user1@example.com",
    "password": "user1pw"
}
```

Example Response:

```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0a...",
    "token_type": "Bearer"
}
```

Include the access token in the Authorization header of subsequent requests to authenticate yourself.

### Task Endpoints

- **GET /api/v1/tasks**: Retrieve all tasks belonging to the authenticated user.
- **POST /api/v1/tasks**: Create a new task.
- **GET /api/v1/tasks/{id}**: Retrieve a specific task.
- **PUT /api/v1/tasks/{id}**: Update a task.
- **DELETE /api/v1/tasks/{id}**: Delete a task.

### Additional Task Endpoints

- **GET /api/v1/tasks/overdue**: Retrieve all tasks that everdue (admin see all).
- **GET /api/v1/tasks/{id}/project**: See the project of a task.
- **PATCH /api/v1/tasks/{task}/deadline**: Update the the deadline of a project (admin can all).

### Project Endpoints

- **POST /api/v1/projects**: Create a new task.
- **GET /api/v1/projects/{id}**: Show all projects.
- **POST /api/v1/projects/{id}**: Create a project.
- **PUT /api/v1/projects/{id}**: Update a project.
- **DELETE /api/v1/projects/{id}**: Delete a project.
- **GET /api/v1/projects/{id}/tasks**: Get the tasks associated with a project.

### User Endpoints

- **POST /api/v1/users/{id}/tasks**: Get all tasks for a user (admin all).

## Testing

This project includes PHPUnit tests to ensure the correctness of the API functionality. To run the tests, use the following command:

```
vendor/bin/sail artisan test
```

It includes two test suites. One for the normal CRUD functionality. One for checking sanctum token abilities
work correctly.


### Documentation of the api

I used knuckleswtf/scribe package to create the OpenAPI Documentaion.

```
composer require --dev knuckleswtf/scribe
```

I created the documentation with:

```
sail artisan scribe:generate
```

you can access the documentation under

```
localhost/docs
```

### Import Routes in Postman

You will find the routes for the import in postman under:
```
{project_root}/public/docs/collection.json
```

# TODO List

## Priority Tasks:

1. **Avoid using foreign keys in `fillable` methods**:
   - ~~Remove user_id from fillable array.~~

2. **Use `$request->validated()` for validated request parameters**:
   - ~~Replace usages of `$request->safe()->only()` with `$request->validated()` to only retrieve validated request parameters.~~

3. **Group routes where possible**:
   - Group related routes using Laravel route grouping to improve code organization and readability.

## Nice-to-have Tasks (Prioritize if time permits):

1. **Consider using Invokeable Controllers**:
   - ~~Refactor controllers to use Invokeable Controllers where appropriate to further separate concerns and improve code readability.~~

2. **Move business logic out of controllers**:
   - ~~Implement Service or Action classes to encapsulate business logic and remove it from controllers to adhere to the "fat model, skinny controller" principle.~~


## Additional Tasks:

1. **Remove unused classes from the Use-Statements**:
   - ~~Identify and remove any unused classes from the Use-Statements to declutter the codebase and improve maintainability.~~


### Notes

This would have normally been in my middleware: 

```
abort_if(! auth()->user()->tokenCan('task-list'), 403); 
```

But as laravel now provides these two middlewares:

```
CheckAbilities::class
CheckForAnyAbility::class
```

which were enabled, I did not implement the auth middleware specifically.

### Optimizations

I added a TaskIndexResource that limits the output related to the Description. After that 
I created an index to the state field to speed up queries here.

To test the performance on the tasks/overdue route, I recommend the following procedure. The initial profiling
should be done as follows:

1. Log in with the user who has the most tasks:

```
curl -X POST \
   --location 'http://localhost/api/login' \
   --header 'Content-Type: application/json' \
   --header 'Accept: application/json' \
   --data-raw '{"email": "user3@example.com","password":"user3pw"}'
```
The reponse on success will be:

```json
{
   "access_token":"2|V26mOaLIDlTJ7sO8AWv31LDBHRiBGX1f...",
   "token_type":"Bearer"
}
```

2. Profiling of the route tasks/overdue:

```bash
ab -n 100 -c 10 \ 
   -H 'Accept-Encoding: gzip, deflate' \
   -H 'Content-Type: application/json' \
   -H 'Accept: application/json' \
   -H 'Authorization: Bearer 2|V26mOaLIDlTJ7sO8AWv31LDBHRiBGX1f...' \
   http://localhost/api/v1/tasks/overdue
```

3. Store the value of the profiling. After that apply your optimization measures. For example:

```bash
> sail composer require laravel/octane
> sail artisan octane:install
``` 

4. Repeat step 2.

```bash
ab -n 100 -c 10 \ 
   -H 'Accept-Encoding: gzip, deflate' \
   -H 'Content-Type: application/json' \
   -H 'Accept: application/json' \
   -H 'Authorization: Bearer 2|V26mOaLIDlTJ7sO8AWv31LDBHRiBGX1f...' \
   http://localhost/api/v1/tasks/overdue
```

## Contributors

- Me

## License

This project is licensed under the MIT License - see the License file for details.

### Steps I made to build the api

- add alias: alias sail=vendor/bin/sail to make life easier :)
- change CACHE_STORAGE in .env to redis
- change engine entry in config/database.php to use ROW_FORMAT=dynamic
- enable debugging:
- add SAIL_XDEBUG_MODE=develop,debug in .env
- add SAIL_XDEBUG_CONFIG="client_host=localhost" in .env
- as Laravel 11.0 has no api route file, create it: sail artisan install:api (which installs sanctum too),
  allow to run the migrations too.
- check if the engine command use ROW_FORMAT=dynamic
```
- sail artisan mysql -h mysql
```
then run:
```
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
```
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
- sail artisan make:middleware AlwaysAcceptJson
- create documentation: 
composer require --dev knuckleswtf/scribe
sail artisan vendor:publish --tag=scribe-config
sail artisan scribe:generate
- sail artisan make:migration add_deadline_to_task_table --table=tasks
- sail artisan make:migration create_projects_table
- sail artisan make:controller  Api/V1/Project{Index, Create, Read, Update, Delete}Controller --invokable
- sail artisan make:test Api/V1/ProjectTest
- sail sail artisan make:request V1/StoreProjectRequest
- sail artisan make:class Api/V1/Actions/{Index, Show, Delete}ProjectAction
- sail artisan make:controller Api/V1/Projects/ProjectTasksController --invokable
- sail artisan make:resource V1/ProjectTasksResource
- sail artisan make:test Api/V1/UserTasksTest
- sail artisan make:controller Api/V1/Users/UserTasksController --invokable
- sail artisan make:resource V1/UserTasksResource
- sail artisan make:controller Api/V1/Tasks/TaskUpdateController --invokable
- sail artisan make:request V1/UpdateTaskRequest
- sail artisan make:class Api/V1/Actions/UpdateTaskAction
- sail artisan make:request V1/PatchTaskRequest
- sail artisan make:test Api/V1/TaskOverdueTest
- sail artisan make:controller Api/V1/Tasks/TaskOverdueController --invokable
- sail artisan make:enum Auth/Roles/Role --int
- sail artisan make:event TaskUpdated
- sail artisan make:test Api/V1/TaskEventListenerTest
- sail artisan make:mail DeadlineBreachedEmail
- sail artisan make:view deadline.expired
Optimization (create specific index resource to limit the output of description to 50 chars)
- sail artisan make:resource V1/TaskIndexResource
- sail artisan make:controller Api/V1/Tasks/TaskIndexController --invokable
- sail artisan make:migration add_index_for_state_on_tasks --table=tasks (add index on state).
Final Steps:
sail artisan make:enum Auth/Token/UserTasksToken

- TOD0: 
    * Rename CanEditDeadLines -> CheckTaskUpdateAuthorization
    * Remove .scripd folder from project add to .gitignore
    * Enum Cast in Model
    * session.php: 'driver' => env('SESSION_DRIVER', 'redis'),
