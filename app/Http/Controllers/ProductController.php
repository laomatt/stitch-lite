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

				$products = $sh->call($args)->products;
				$prods = [];
				foreach ($products as $key => $value) {
					foreach ($value->variants as $k => $variant) {
						// create variant items if skus doesn't exist
						$product = Product::where("sku","=", $variant->sku);
						if ($product->count() > 0) {
							$product = $product->firstOrFail();
						} else {
							$product = new Product;
							$product->name = $value->title." (".$variant->title.")";
							$product->sku = $variant->sku;
							$product->price = $variant->price;
							$product->quantity = $variant->inventory_quantity;
						}

						// TODO: log all updating
						// return var_dump(Product::where("sku","=", 'bat-555')->count());
						// $product->save();

					}
				}

				return var_dump(Product::all());
    }

    	public function listProductsVend()
    {
			$vend = new VendAPI\VendAPI('https://mattsexample.vendhq.com','Bearer','KWDZNSo67gRgRdxANKYrG_hyDFMY9MH5WM4yOrhA');
			$vends = [];
			foreach ($vend->getProducts() as $key => $value) {
				// array_push($vends, $key);
			}
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
    	// cycle through all shopify products
    	
    	// $product = new Product;
    	// $product->name = Input::get('name');
    	// $product->sku = Input::get('email');
    	// $product->quantity = Input::get('password');
    	// $product->price = Input::get('password');
    }

    public function update(){
    	// get all products from shopify

    	// get all products from vend
    }


    public function show(){}

    private function requestTokenRefresh()
    {

    }
 }