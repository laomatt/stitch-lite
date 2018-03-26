<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Html\Form;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use App;
use VendAPI;

class ProductController extends BaseController {

	// gets and updates products from shopify API
	private function listProductsShopify()
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
						$product = Product::where("sku","=", $variant->sku)->first();
						if ($product->count() > 0) {
							$product = $product->firstOrFail();
							$current_quantity = $product->quantity;
							$api_quantity = $variant->inventory_quantity;
							$quantity = min($current_quantity,$api_quantity);
							// $quantity = 900;
						} else {
							// create variant items if skus doesn't exist
							$quantity = $variant->inventory_quantity;
							$product = new Product;
						}


						$product->name = $value->title." (".$variant->title.")";
						$product->sku = $variant->sku;
						$product->price = $variant->price;
						$product->quantity = $quantity;
						$product->save();

						// TODO: log all updating

					}
				}

    }

  // gets and updates products from Vend API
	private function listProductsVend()
    {
			$vend = new VendAPI\VendAPI('https://mattsexample.vendhq.com','Bearer','KWDZNSo67gRgRdxANKYrG_hyDFMY9MH5WM4yOrhA');
			$vends = [];
			// return var_dump($vend->getProducts()[16]->inventory[0]->count);
			// return var_dump($vend->getProducts()[16]->base_name);
			foreach ($vend->getProducts() as $key => $value) {
				$name = $value->base_name;
				$variant_title = $value->variant_option_one_value;
				$sku = $value->sku;
				$price = $value->price;

				$product = Product::where("sku","=",$sku)->first();
				
				if (is_null($value->inventory[0])) {
					continue;
				}
				if ($product->count() > 0) {
					$product = $product->firstOrFail();
					$current_quantity = $product->quantity;
					$api_quantity = $value->inventory[0]->count;
					$quantity = min($current_quantity,$api_quantity);
				} else {
					$product = new Product;
					$quantity = $value->inventory[0]->count;
				}

				$product->name = $name." (".$variant_title.")";
				$product->sku = $sku;
				$product->price = $price;
				$product->quantity = $quantity;
				$product->save();
			}

			// TODO: if the token is expired, then we must request refresh of token
			// TODO: log all updating

    }


    public function index()
    {
    	// cycle through all shopify products
    	return Product::all();

    }

    private function syncShopify($data)
    {
    	$apiKey = env('SHOPIFY_KEY');
  		$apiSecret = env('SHOPIFY_SECRET');
  		$access = env('SHOPIFY_TOKEN');

			$sh = App::make(
				'ShopifyAPI',
	    	['API_KEY' => $apiKey, 'API_SECRET' => $apiSecret]
			);

			// went through the entire token getting process 5 times, this token still wouldn't update to 'write_product' scope
			$sh->setup(['SHOP_DOMAIN' => 'mattsteststore2.myshopify.com', 'ACCESS_TOKEN' => '2ef2cf6e0023e84eeebe999c0da58962']);
			
			$list_args = [
					'ALLDATA' => true,
					'URL' => "/admin/products.json",
			];
			$prods = [];
			// get a list of current products of shopify
			$products = $sh->call($list_args)->products;
			foreach ($products as $key => $value) {
				foreach($value->variants as $k => $variant){
					$sku = $variant->sku;
					$id = $variant->id;

					// get product quantity from the products in the DB by sku
					$product = Product::where("sku","=", $sku);
					$current_quantity = $product->first()->quantity;
					$args = [
							'ALLDATA' => true,
							'URL' => "/admin/variants/".$id.".json",
							'METHOD' => 'PUT',
							'DATA' => [
						   		"variant" => [
						  							    "id" => $id,
						  							    "inventory_quantity" => $current_quantity
						  							  ]
						  					]
								  
					];

					array_push($prods, $current_quantity);
					// change the quanitity
					$sh->call($args);
				}
			}

			// TODO:
			// find all products with sku's not in $prods
			// push that product up
    	return var_dump(Product::all());
    }

    private function syncVend($data)
    {
	    	// TODO:
		   // check if this sku exists in Vend
				// if not 
					// add the record
				// else
		    	// update record    	
    }

    public function example(){
    	return var_dump(Product::all());
    }

    // Syncs all products, updates quantities to the lowest quantity found in the APIs
    public function sync()
    {
    	// get all products from shopify to the database
    	$this::listProductsShopify();

    	// get all products from vend to the database
    	$this::listProductsVend();

    	// cycle through the products table
    	$products = Product::all();

    	$vends = [];
    	foreach ($products as $key => $product) {
	    	// push up to Shopify
		    	$this::syncShopify($product);
	    	// push up to Vend
		    	$this::syncVend($product);
    	}
    	
			return var_dump($vends);

    }

    // displays information for one product
    public function show($id)
    {
    	$product = Product::find($id);
    	return $product;
    }


    private function requestTokenRefresh()
    {

    }
 }