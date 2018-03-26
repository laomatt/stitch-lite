<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/api/products', 'ProductController@index');
Route::get('/api/product/{id}', 'ProductController@show');
Route::get('/api/sync', 'ProductController@sync');

// TODO: add end point to update quantity from the back end
// Route::put('/api/product/{id}', 'ProductController@update');


