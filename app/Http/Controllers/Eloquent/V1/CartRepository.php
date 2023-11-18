<?php

namespace App\Http\Controllers\Eloquent\V1;

use App\Http\Controllers\Interfaces\V1\AddressesRepositoryInterface;
use App\Http\Controllers\Interfaces\V1\CartRepositoryInterface;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Slider;
use League\Flysystem\Config;
use JWTAuth;
use TymonJWTAuthExceptionsJWTException;

class CartRepository implements CartRepositoryInterface
{

    public function getCart($request)
    {
        if (auth('api')->check()) {

            $data = Cart::where('user_id', auth('api')->user()->id)->with('product')->orderBy('id', 'desc')->get();
        } else {
            $data = Cart::where('visitor_id', $request['visitor_id'])->with('product')->orderBy('id', 'desc')->get();
        }
        return $data;
    }

    public function plusCart($request, $id)
    {
        $data = Cart::where('id', $id)->first();
        if ($data) {
            $data->qty += 1;
            $data->save();
        }
        return $data;
    }

    public function minusCart($request, $id)
    {
        $data = Cart::where('id', $id)->first();
        if ($data) {
            if ($data->qty <= 1) {
                $data->delete();
            } else {
                $data->qty -= 1;
                $data->save();
            }
        }
        return $data;
    }

    public function deleteCart($request, $id)
    {
        $data = Cart::where('id', $id)->first();
        if ($data) {
            $data->delete();
        }
        return $data;
    }

    public function addCart($request)
    {
        if (auth('api')->check()) {
            //if user login
            $data = Cart::where('user_id', auth('api')->user()->id)
                ->where('product_id', $request['product_id'])
                ->first();
            if ($data) {
                $data->qty += $request['qty'];
                $data->save();
            } else {
                $data = Cart::create([
                    'user_id' => auth('api')->user()->id,
                    'product_id' => $request['product_id'],
                    'qty' => $request['qty'],
                ]);
            }
        } else {
            //if user is visitor
            $data = Cart::where('visitor_id', $request['visitor_id'])
                ->where('product_id', $request['product_id'])
                ->first();
            if ($data) {
                $data->qty += $request['qty'];
                $data->save();
            } else {
                $data = Cart::create([
                    'visitor_id' => $request['visitor_id'],
                    'product_id' => $request['product_id'],
                    'qty' => $request['qty'],
                ]);
            }
        }
        return $data;
    }

}
