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

// Route::post('user', 'UserController@createUser');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/shopify', 'ProductController@listProductsShopify');
Route::get('/example', 'ProductController@example');

Route::get('/api/products', 'ProductController@index');
Route::get('/api/product/{id}', 'ProductController@show');

// TODO: add end point to update quantity from the back end
// Route::put('/api/product/{id}', 'ProductController@update');

Route::get('/api/sync', 'ProductController@sync');

