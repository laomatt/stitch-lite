<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Product extends Eloquent
{
	public $name = "";
	public $sku = "";
	public $price = 0.00;
	public $quantity = 0;

  public function __construct($name,$sku)
  {

		// – Product Name 
		// – SKU
		// – Quantity
		// – Price

  	$this->name = $name;
  	$this->sku = $sku;
  	$this->quantity = $quantity;
  	$this->price = $price;
  }
  
}
