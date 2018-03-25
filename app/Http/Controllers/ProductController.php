<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Html\Form;
use Illuminate\Http\Response;
use App;
use VendAPI;
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
    	$token = env('VEND_ACCESS_TOKEN');
    	$type = env('VEND_TOKEN_TYPE');
			$vend = new VendAPI\VendAPI('https://mattsexample.vendhq.com','Bearer','KWDZNSo67gRgRdxANKYrG_hyDFMY9MH5WM4yOrhA');
			return var_dump($vend->getProducts());

				// TODO: if the token is expired, then we must request refresh of token

				// try {
					
				// } catch (Exception $e) {
					
				// 	return 'dsfsd';
				// }
				// return var_dump($vend->request());
				// $vend->getProducts();
				// return var_dump(app('Illuminate\Http\Response')->status());
    }

    public function index(){
    	// 

    	// $product = new Product;
    	// $product->name = Input::get('name');
    	// $product->sku = Input::get('email');
    	// $product->quantity = Input::get('password');
    	// $product->price = Input::get('password');
    }

    public function update(){}

    
    public function show(){}

    private function requestTokenRefresh()
    {

    }
 }