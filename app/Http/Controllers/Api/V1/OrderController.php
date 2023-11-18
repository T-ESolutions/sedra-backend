<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interfaces\V1\AddressesRepositoryInterface;
use App\Http\Controllers\Interfaces\V1\OrderRepositoryInterface;
use App\Http\Requests\V1\User\AddressRequest;
use App\Http\Requests\V1\User\Order\ApplyCouponRequest;
use App\Http\Requests\V1\User\Order\OrderRequest;
use App\Http\Requests\V1\User\Order\updateAddressRequest;
use App\Http\Resources\V1\User\Order\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    protected $targetRepo;

    public function __construct(OrderRepositoryInterface $targetRepo)
    {
        $this->targetRepo = $targetRepo;
    }

    public function placeOrder(OrderRequest $request)
    {
        $request = $request->validated();
        $data = $this->targetRepo->placeOrder($request);
        if ($data == "cart_empty") {
            return response()->json(msg(failed(), trans('lang.cart_empty')));
        }
        if ($data == "coupon_used_before") {
            return response()->json(msg(failed(), trans('lang.coupon_used_before')));
        }
        $result['order_id'] = $data->id;
        return response()->json(msgdata(success(), trans('lang.success'), $result));
    }

    public function updateAddress(updateAddressRequest $request)
    {
        $data = $this->targetRepo->updateAddress($request);
        if ($data == 'not_possible') {
            return response()->json(msg(failed(), trans('lang.not_possible_change_address')));

        }
        return response()->json(msg(success(), trans('lang.success')));
    }

    public function applyCoupon(ApplyCouponRequest $request)
    {
        $data = $this->targetRepo->applyCoupon($request);
        if ($data == "coupon_expired") {
            return response()->json(msg(failed(), trans('lang.coupon_expired')));
        }

        if ($data == "cart_empty") {
            return response()->json(msg(failed(), trans('lang.cart_empty')));
        }
        if ($data == "coupon_used_before") {
            return response()->json(msg(failed(), trans('lang.coupon_used_before')));
        }
        return response()->json(msgdata(success(), trans('lang.success'), $data));
    }

    public function myOrders(Request $request)
    {
        $data = $this->targetRepo->myOrders($request);
        $data = (OrderResource::collection($data))->response()->getData(true);
        return response()->json(msgdata(success(), trans('lang.success'), $data));
    }

    public function orderDetails(Request $request, $id)
    {
        $data = $this->targetRepo->orderDetails($request, $id);
        if ($data != null) {
            $data = OrderResource::make($data);
            return response()->json(msgdata(success(), trans('lang.success'), $data));
        } else {
            return response()->json(msg(not_found(), trans('lang.not_found')));
        }
    }

    public function deleteOrder(Request $request, $id)
    {
        $data = $this->targetRepo->cancelOrder($request, $id);
        if ($data == "not_found") {
            return response()->json(msg(not_found(), trans('lang.not_found')));
        } elseif ($data == "cannot_delete") {
            return response()->json(msg(failed(), trans('lang.cannot_delete')));
        }
        $data = OrderResource::make($data);
        return response()->json(msgdata(success(), trans('lang.success'), $data));
    }


    public function print_order(Order $order)
    {
        return view('exports.order_pdf', compact('order'));
    }


}
