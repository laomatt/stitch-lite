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
				$difference_hash = [];
				foreach ($products as $key => $value) {
					foreach ($value->variants as $k => $variant) {
						$product = Product::where("sku","=", $variant->sku);
						if ($product->count() > 0) {
							$product = $product->firstOrFail();
							$current_quantity = $product->quantity;
							$api_quantity = $variant->inventory_quantity;
							$difference = abs($current_quantity - $api_quantity);
							// MISTAKE::  should only save this number somewhere else,
							$quantity = $current_quantity - $difference;
						} else {
							// create variant items if skus doesn't exist
							$quantity = $variant->inventory_quantity;
							$product = new Product;
						}


						$product->name = $value->title." (".$variant->title.")";
						$product->sku = $variant->sku;
						$product->price = $variant->price;
						$difference_hash[$product->sku] = $quantity;
						$product->save();

						// TODO: log all updating

					}
				}

				return $difference_hash;

    }

  // gets and updates products from Vend API
	private function listProductsVend()
    {
			$vend = new VendAPI\VendAPI('https://mattsexample.vendhq.com','Bearer','KWDZNSo67gRgRdxANKYrG_hyDFMY9MH5WM4yOrhA');
			$vends = [];
			$difference_hash = [];
			foreach ($vend->getProducts() as $key => $value) {
				$name = $value->base_name;
				$variant_title = $value->variant_option_one_value;
				$sku = $value->sku;
				$price = $value->price;

				$product = Product::where("sku","=",$sku);
				
				if (is_null($value->inventory[0])) {
					continue;
				}

				// TODO: centralize this code somewhere else
				if ($product->count() > 0) {
					$product = $product->firstOrFail();
					$current_quantity = $product->quantity;
					$api_quantity = $value->inventory[0]->count;
					// we want to take the difference between our current min and the what in the API
					$difference = abs($current_quantity - $api_quantity);
					$quantity = $current_quantity - $difference;
				} else {
					$product = new Product;
					$quantity = $value->inventory[0]->count;
				}

				$product->name = $name." (".$variant_title.")";
				$product->sku = $sku;
				$product->price = $price;
				$difference_hash[$product->sku] = $quantity;
				$product->save();
			}

			// TODO: if the token is expired, then we must request refresh of token
			// TODO: log all updating
			return $difference_hash;

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
			// $sh->setup(['SHOP_DOMAIN' => 'mattsteststore2.myshopify.com', 'ACCESS_TOKEN' => $access]);
			
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

    // Syncs all products, updates quantities to the lowest quantity found in the APIs
    public function sync()
    {
    	// get all products from shopify to the database
    	$difference = [];
    	$diff_hash = $this::listProductsShopify();
    	foreach ($diff_hash as $sku => $diff) {
    		if (isset($difference[$sku])) {
    			$difference[$sku] += $diff;
    		} else {
    			$difference[$sku] = $diff;
    		}
    	}

    	// get all products from vend to the database
    	$diff_hash = $this::listProductsVend();

    	foreach ($diff_hash as $sku => $diff) {
    		if (isset($difference[$sku])) {
    			$difference[$sku] += $diff;
    		} else {
    			$difference[$sku] = $diff;
    		}
    	}

    	// update db records

    	foreach ($difference as $sku => $diff) {
    		$product = Product::where("sku","=", $sku);
    		$quantity = $product->quantity;
    		$quantity -= $diff;
    		$product->quantity = $quantity;
    		$product->save();
    	}

    	// TODO: want a section to push up database quanities to each API
    	// cycle through the products table
    	// $products = Product::all();
    	// foreach ($products as $key => $product) {
	    	// push up to Shopify
		    	// $this::syncShopify($product);
	    	// push up to Vend
		    	// $this::syncVend($product);
    	// }
    	
			return response()->json([
			    'code' => '200',
			    'message' => 'API successfully synced'
			]);

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