<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\BrazilStateController;
use App\Http\Controllers\ConsultController;
use App\Http\Controllers\MedicalDataController;
use App\Http\Controllers\UserController;

/** @var \Laravel\Lumen\Routing\Router $router */

/// Rotas da model Doctor
$router->post('/doctors', 'DoctorController@store');
$router->get('/doctors', 'DoctorController@show');
$router->put('/doctors', 'DoctorController@update');
$router->delete('/doctors', 'DoctorController@destroy');
$router->get('/doctors/hours', 'DoctorController@hours');
# Middleware faz com que as rotas só possam ser acessadas se o usuário estiver autorizado.
$router->get('/doctors/filter', ['middleware' => 'auth', 'uses' => 'DoctorController@doctors']);
$router->get('/especialities', 'DoctorController@especialities');

/// Rota da model BrazilState
$router->get('/states', 'BrazilStateController@show');

/// Rota da model Consult
$router->post('/consults', ['middleware' => 'auth', 'uses' => 'ConsultController@store']);
$router->get('/consults/hours',['middleware' => 'auth', 'uses' => 'ConsultController@showHours']);
$router->get('/consults/{id}', ['middleware' => 'auth', 'uses' => 'ConsultController@show']);
$router->get('/consults-index', ['middleware' => 'auth', 'uses' => 'ConsultController@index']);
$router->delete('/consults', ['middleware' => 'auth', 'uses' => 'ConsultController@destroy']);

/// Rota da model MedicalData
$router->post('/medical-data', ['middleware' => 'auth', 'uses' => 'MedicalDataController@store']);
$router->put('/medical-data', ['middleware' => 'auth', 'uses' => 'MedicalDataController@update']);
$router->delete('/medical-data/{id}', ['middleware' => 'auth', 'uses' => 'MedicalDataController@destroy']);
$router->get('/medical-data/{id}', ['middleware' => 'auth', 'uses' => 'MedicalDataController@show']);
$router->get('/medical-data-index', ['middleware' => 'auth', 'uses' => 'MedicalDataController@index']);

/// Rota da model User
$router->post('/register', 'UserController@register');
$router->post('/login', 'UserController@login');
$router->put('/logout', ['middleware' => 'auth', 'uses' => 'UserController@logout']);