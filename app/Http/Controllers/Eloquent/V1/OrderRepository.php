<?php
/**
 * Created by PhpStorm.
 * User: Al Mohands
 * Date: 22/05/2019
 * Time: 01:53 م
 */

namespace App\Http\Controllers\Eloquent\V1;

use App\Http\Controllers\Interfaces\V1\OrderRepositoryInterface;
use App\Models\CouponUsage;
use App\Models\OrderDetail;
use App\Models\Visitor;
use App\Models\Address;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Cart;
use App\Models\User;
use Carbon\Carbon;
use JWTAuth;
use TymonJWTAuthExceptionsJWTException;

class OrderRepository implements OrderRepositoryInterface
{

    public function placeOrder($request)
    {
        $discount = 0;
        $sub_total = 0;
        $shipping_cost = 0;
        $coupon = null;
        if( isset($request['coupon_code'])) {
            $coupon = Coupon::where('code', $request['coupon_code'])
                ->active()
                ->where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now())
                ->first();
        }
        if (auth('api')->check()) {
            //auth section
            $user = User::whereId(auth('api')->user()->id)->first();
            //cart
            $carts = Cart::where('user_id', auth('api')->user()->id)->get();
            foreach ($carts as $cart) {
                $sub_total += ($cart->product->price - ($cart->product->price * $cart->product->discount / 100)) * $cart->qty;
            }
            if ($carts->count() == 0) {
                return "cart_empty";
            }
            //address
            $address = Address::whereId($request['address_id'])->first();
            $shipping_cost = $address->country->shipping_cost;
            if ($user->shipping_free) {
                $shipping_cost = 0;
            }

            if ($user->discount == 0) {
                if ($coupon) {
                    //check if user used coupon or not
//                    $exists_usage = CouponUsage::where('user_id', $user->id)->where('coupon_id', $coupon->id)->first();
//                    if ($exists_usage) {
//                        return "coupon_used_before";
//                    }
                    if ($coupon->type == Coupon::PERCENTAGE) {
                        $discount = $sub_total * $coupon->discount / 100;
                    } elseif ($coupon->type == Coupon::AMOUNT) {//amount
                        $discount = $coupon->discount;
                    }
                }
            } else {
                $discount += $sub_total * $user->discount / 100;
            }
            $order_data['user_id'] = $user->id;

        } else {

            //un auth section

            //cart
            $carts = Cart::where('visitor_id', $request['visitor_id'])->get();
            foreach ($carts as $cart) {
                $sub_total += ($cart->product->price - ($cart->product->price * $cart->product->discount / 100)) * $cart->qty;
            }
            if ($carts->count() == 0) {
                return "cart_empty";
            }
            if ($coupon) {
                //check if user used coupon or not
//                    $exists_usage = CouponUsage::where('user_id', $user->id)->where('coupon_id', $coupon->id)->first();
//                    if ($exists_usage) {
//                        return "coupon_used_before";
//                    }
                if ($coupon->type == Coupon::PERCENTAGE) {
                    $discount = $sub_total * $coupon->discount / 100;
                } elseif ($coupon->type == Coupon::AMOUNT) {//amount
                    $discount = $coupon->discount;
                }
            }
            //generate visitor address
            $address_arr['f_name'] = $request['f_name'];
            $address_arr['phone'] = $request['phone'];
            $address_arr['address'] = $request['address'];
            $address_arr['country_id'] = (int)$request['country_id'];
            $country = Country::whereId($request['country_id'])->first();
            $address_arr['country'] = $country;
            $address = (object)$address_arr;
            //get shipping cost by selected country
            $shipping_cost = $country->shipping_cost;

            $order_data['visitor_id'] = $request['visitor_id'];

            $visitor_data['f_name'] = $request['f_name'];
            $visitor_data['phone'] = $request['phone'];
            $visitor_data['address'] = $request['address'];
            $visitor_data['country_id'] = $request['country_id'];
            Visitor::where('id', $request['visitor_id'])->update($visitor_data);
        }


        $total = $sub_total - $discount;
        $total += $shipping_cost;
        if ($request['payment_type'] == Order::PAYMENT_TYPE_CASH) {
            $payment_status = Order::PAYMENT_STATUS_NOT_PAID;
        } else {
            $payment_status = Order::PAYMENT_STATUS_PAID;
        }
        $max_id = Order::get()->max('id');
        if ($max_id < 76000) {
            $order_id = 76000;
            $order_data['id'] = $order_id;
        }

        $order_data['address'] = $address;
        $order_data['coupon'] = $coupon;
        $order_data['subtotal'] = $sub_total;
        $order_data['discount'] = $discount;
        $order_data['shipping_cost'] = $shipping_cost;
        $order_data['total'] = $total;
        $order_data['payment_status'] = $payment_status;
        $order_data['payment_type'] = $request['payment_type'];
        $order_data['status'] = Order::STATUS_PENDING;
        $order_data['notes'] = $request['notes'];
        $order = Order::create($order_data);

        foreach ($carts as $cart) {
            $price = $cart->product->price - ($cart->product->price * $cart->product->discount / 100);
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $cart->product_id,
                'qty' => $cart->qty,
                'price' => $price,
                'total' => $price * $cart->qty,
            ]);
        }
        if ($order && $discount > 0) {
            if ($coupon) {
                $coupon->usage_count = $coupon->usage_count + 1;
                $coupon->save();

//                    $coupon_usage_data['user_id'] = $user->id;
//                    $coupon_usage_data['coupon_id'] = $coupon->id;
//                    CouponUsage::create($coupon_usage_data);
            }
        }
        if (auth('api')->check()) {
            //empty cart
            Cart::where('user_id', auth('api')->user()->id)->delete();
        } else {
            Cart::where('visitor_id', $request['visitor_id'])->delete();
        }
        return $order;
    }

    public function updateAddress($request)
    {
        $user = JWTAuth::user();
        $order = Order::whereId($request->order_id)->first();
        if ($order->status == Order::STATUS_SHIPPED ||
            $order->status == Order::STATUS_DELIVERED ||
            $order->status == Order::STATUS_REJECTED ||
            $order->status == Order::STATUS_CANCELLED_BY_USER ||
            $order->status == Order::STATUS_CANCELLED_BY_ADMIN) {
            return 'not_possible';

        }
        $address = Address::whereId($request->address_id)->first();
        $shipping_cost = $address->country->shipping_cost;
        if ($user->shipping_free) {
            $shipping_cost = 0;
        }
        $order->shipping_cost = $shipping_cost;
        $order->total = $order->subtotal + $shipping_cost - $order->discount;
        $order->address = $address;
        $order->save();
        return $order;
    }

    public function applyCoupon($request)
    {
        //todo::nasser
        if (auth('api')->check()) {

            $user_id = auth('api')->user()->id;
            $carts = Cart::where('user_id', $user_id)->get();
        } else {
            $carts = Cart::where('visitor_id', $request['visitor_id'])->get();
        }
        if ($carts->count() == 0) {
            return "cart_empty";
        }
        $sub_total = 0;
        foreach ($carts as $cart) {
            $sub_total += ($cart->product->price - ($cart->product->price * $cart->product->discount / 100)) * $cart->qty;
        }
        $coupon = Coupon::where('code', $request->coupon_code)
            ->active()
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->first();

        $discount = 0;
        if ($coupon) {

            //check if user used coupon or not
//            $exists_usage = CouponUsage::where('user_id', $user_id)->where('coupon_id', $coupon->id)->first();
//            if ($exists_usage) {
//                return "coupon_used_before";
//            }

            if ($coupon->type == Coupon::PERCENTAGE) {
                $discount = $sub_total * $coupon->discount / 100;
            } elseif ($coupon->type == Coupon::AMOUNT) {//amount
                $discount = $coupon->discount;
            } else {//amount
                $discount = 0;
            }
        }else{
            return "coupon_expired";
        }

        $result['sub_total'] = $sub_total;
        $result['discount'] = $discount;
        $result['final_total'] = $sub_total - $discount;
        return $result;
    }

    public function myOrders($request)
    {
        $order = Order::Query();
        if ($request->status == 'current') {
            $order = $order->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_ON_PROGRESS, Order::STATUS_SHIPPED]);
        } elseif ($request->status == 'previous') {
            $order = $order->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_REJECTED, Order::STATUS_CANCELLED_BY_USER, Order::STATUS_CANCELLED_BY_ADMIN]);
        } else {
            $order = $order->where('status', Order::STATUS_DELIVERED)
                ->orWhere('status', Order::STATUS_REJECTED)
                ->orWhere('status', Order::STATUS_CANCELLED_BY_USER)
                ->orWhere('status', Order::STATUS_CANCELLED_BY_ADMIN);
        }
        $order = $order->where('user_id', JWTAuth::user()->id)->orderBy('id', 'desc')->paginate(Config('app.paginate'));
        return $order;
    }

    public function orderDetails($request, $id)
    {
        $order = Order::where('user_id', JWTAuth::user()->id)
            ->where('id', $id)
            ->with('orderDetails')
            ->first();


        return $order;
    }

    public function cancelOrder($request, $id)
    {
        $order = Order::where('user_id', JWTAuth::user()->id)
            ->where('id', $id)
            ->with('orderDetails')
            ->first();

        if ($order) {
            if ($order->status == Order::STATUS_PENDING) {
                $order->status = Order::STATUS_CANCELLED_BY_USER;
                $order->save();
            } else {
                return "cannot_delete";
            }
        } else {
            return "not_found";
        }

        return $order;
    }

}
