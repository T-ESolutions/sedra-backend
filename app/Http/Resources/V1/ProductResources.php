<?php

namespace App\Http\Resources\V1;

use App\Models\Cart;
use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */


    public function toArray($request)
    {
        //generate price after discount
        if ($this->discount > 0) {
            $discount = $this->discount / 100;
            $discount_price = $this->price * $discount;
            $final_price = $this->price - $discount_price;
        } else {
            $final_price = $this->price;
        }

        //get quantity from cart ...
        $qty_in_cart = 0;
        $cart_id = 0;
        if (auth('api')->check()) {
            $cart = Cart::where('product_id', $this->id)->where('user_id', auth('api')->user()->id)->first();
        } else {
            $cart = Cart::where('product_id', $this->id)->where('visitor_id', request()->visitor_id)->first();
        }

        if ($cart) {
            $qty_in_cart = $cart->qty;
            $cart_id = $cart->id;
        }


        return [
            'id' => (int)$this->id,
            'title' => (string)$this->title,
            'description' => (string)$this->description,
            'short_description' => (string)$this->short_description,
            'attributes' => (string)$this->attributes,
            'video_url' => (string)$this->video_url,
            'rate' => (double)$this->rate,
            'price' => (double)$this->price,
            'discount' => (double)$this->discount,
            'price_after_discount' => (double)$final_price,
            'categories' => $this->productCategories ? CategoryResources::collection($this->productCategories) : '',
            'images' => $this->productImages ? ProductImagesResources::collection($this->productImages) : '',
            'qty_in_cart' => $qty_in_cart,
            'cart_id' => $cart_id,
        ];
    }


}
