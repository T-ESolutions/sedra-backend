<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interfaces\V1\VisitorRepositoryInterface;
use App\Http\Requests\V1\User\Order\OrderRequest;
use App\Http\Requests\V1\User\VisitorCheckRequest;
use Illuminate\Http\Request;


class VisitorController extends Controller
{
    protected $visitorRepo;

    public function __construct(VisitorRepositoryInterface $visitorRepo)
    {
        $this->visitorRepo = $visitorRepo;
    }

    public function check(VisitorCheckRequest $request)
    {
        $request = $request->validated();
        $data = $this->visitorRepo->check($request);

        return response()->json(msgdata(success(), trans('lang.success'), $data));
    }


}
