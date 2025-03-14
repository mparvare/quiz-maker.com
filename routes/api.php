<?php
use Core\Request;
use Core\Response;
use Src\Controllers\UserController;

// مسیرهای کاربری
$router->addRoute('GET', '/users', function(Request $request, Response $response) {
    $userController = new UserController();
    $userController->getUsers($request, $response);
});

$router->addRoute('GET', '/users/{id}', function(Request $request, Response $response) {
    $userController = new UserController();
    $id = explode('/', $request->getUri())[2];
    $userController->getUserById($request, $response, $id);
});

$router->addRoute('POST', '/users', function(Request $request, Response $response) {
    $userController = new UserController();
    $userController->createUser($request, $response);
});

$router->addRoute('PUT', '/users/{id}', function(Request $request, Response $response) {
    $userController = new UserController();
    $id = explode('/', $request->getUri())[2];
    $userController->updateUser($request, $response, $id);
});

$router->addRoute('DELETE', '/users/{id}', function(Request $request, Response $response) {
    $userController = new UserController();
    $id = explode('/', $request->getUri())[2];
    $userController->deleteUser($request, $response, $id);
});

$router->addRoute('POST', '/login', function(Request $request, Response $response) {
    $userController = new UserController();
    $userController->login($request, $response);
});