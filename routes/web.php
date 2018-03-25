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
Route::get('/vend', 'ProductController@listProductsVend');

Route::get('/api/products', 'ProductController@index');
Route::get('/api/sync', 'ProductController@update');
Route::get('/api/sync', 'ProductController@show');

