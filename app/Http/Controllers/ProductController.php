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

	public function listProductsShopify()
    {
    		$apiKey = env('SHOPIFY_KEY');
    		$apiSecret = env('SHOPIFY_SECRET');
    		$access = env('SHOPIFY_TOKEN');

				$sh = App::make(
					'ShopifyAPI',
		    	['API_KEY' => $apiKey, 'API_SECRET' => $apiSecret]
				);

				$sh->setup(['SHOP_DOMAIN' => 'mattsteststore2.myshopify.com/admin/products.json', 'ACCESS_TOKEN' => $access]);

				$args = [
					'ALLDATA' => true,
					'URL' => 'products.json',
				];

				return var_dump($sh->call($args)->products);
    }

    	public function listProductsVend()
    {


				// return var_dump($sh->call($args)->products);
    }
 }