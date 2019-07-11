<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Общедоступная группа пользователей
$router->group(['prefix' => '/users'], function () use ($router) {

    // Регистрация
    $router->post('/signup', 'UserController@signup');

    // Вход
    $router->post('/signin', 'UserController@signin');

    // Профиль пользователя по id
    $router->get('/show', 'UserController@show');

    // Поиск польователей по username
    $router->get('/search', 'UserController@search');

});

// Общедоступная группа постов
$router->group(['prefix' => '/posts'], function () use ($router) {
    // Просмотр списка
    $router->get('/', 'PostController@index');

    // Просмотр
    $router->get('/show', 'PostController@show');

    // Стена постов
    $router->get('/wall', 'PostController@wall');

    // Поиск постов
    $router->get('/search', 'PostController@search');
});

// Группа под авторизацией
$router->group(['middleware' => 'auth'], function () use ($router) {

    $router->group(['prefix' => '/users'], function () use ($router) {

        // Выйти
        $router->delete('/signout', 'UserController@signout');

        // Мой профиль
        $router->get('/me', 'UserController@me');

        // Обновить профиль
        $router->put('/update', 'UserController@update');

    });

    // Группа постов
    $router->group(['prefix' => '/posts'], function () use ($router) {

        // Создание
        $router->post('/', 'PostController@store');

        // Изменение
        $router->put('/update', 'PostController@update');

        // Удаление
        $router->delete('/delete', 'PostController@delete');
    });

});
