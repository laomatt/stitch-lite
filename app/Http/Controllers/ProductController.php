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

class ProductController extends BaseController {

	public function listProducts()
    {
				$sh = App::make(
					'ShopifyAPI',
					[
						'API_KEY' => env('SHOPIFY_KEY'), 
						'API_SECRET' => env('SHOPIFY_SECRET'), 
						'SHOP_DOMAIN' => 'mattsteststore2.myshopify.com/admin/shop.json', 
						'ACCESS_TOKEN' => env('SHOPIFY_TOKEN')
					]
				);

				// $sh->setup();

				$args = [
					'METHOD' => 'GET',
					'ALLDATA' => true,
				];

				return var_dump($sh->call($args));
				// return var_dump($sh);
    }
 }