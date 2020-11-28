<?php
// Routes

$routes = ['/', '/login', '/login/'];

foreach ($routes as $route) {
    $app->map(['GET', 'POST'], $route, '\App\Controller\UserController:login');
}

$app->group('/admin', function () use ($app) {

    $routes = ['/', '/users'];
    foreach ($routes as $route){
        $app->get($route, '\App\Controller\UserController:index');
    }

    $app->map(['GET', 'POST'], '/users/add', '\App\Controller\UserController:add');
    $app->map(['GET', 'POST'], '/users/edit/[{id}]', '\App\Controller\UserController:edit');

    $app->get('/profiles', '\App\Controller\ProfileController:index');
    $app->map(['GET', 'POST'], '/profiles/add', '\App\Controller\ProfileController:add');
    $app->map(['GET', 'POST'], '/profiles/edit/[{id}]', '\App\Controller\ProfileController:edit');

    $app->get('/profiles/delete/[{id}]', '\App\Controller\ProfileController:delete');
    $app->get('/users/delete/[{id}]', '\App\Controller\UserController:delete');
    $app->get('/users/logout', '\App\Controller\UserController:logout');

})->add(new AuthenticateMiddleware());