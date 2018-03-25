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

Route::get('user/{id}', 'UserController@showProfile');
Route::get('users', 'UserController@listUsers');
Route::get('user', 'UserController@newUser');
Route::post('user', array('before' => 'csrf', function()
{
}));

Route::get('/shopify', 'ProductController@listProductsShopify');
Route::get('/vend', 'ProductController@listProductsVend');

