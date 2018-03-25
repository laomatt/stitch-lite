<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Html\Form;
use App;
// use Illuminate\Html\Form;

class UserController extends BaseController {
		public $layout = 'layout';
    /**
     * Show the profile for the given user.
     */
    public function showProfile($id)
    {
        $user = User::find($id);
        return view('user.profile', array('user' => $user));
    }

		public function listUsers()
    {
				$sh = App::make('ShopifyAPI',['API_KEY' => env('SHOPIFY_KEY'), 'API_SECRET' => env('SHOPIFY_SECRET'), 'SHOP_DOMAIN' => env('SHOPIFY_REDIRECT'), 'ACCESS_TOKEN' => env('SHOPIFY_TOKEN')]);
				// return var_dump($sh);
				// return $sh->installURL(['permissions' => array('write_orders', 'write_products')]);
        // $users = User::all();
        // return view('user.index', array('users' => $users));
    }

   	public function newUser()
    {
        return view('user.new');
    }

    public function createUser($data){
    	$user = new User;

    	$user->name = Input::get('name');
    	$user->email = Input::get('email');
    	$user->password = Input::get('password');

    	$user->save();
    	return redirect('users');

    }

}