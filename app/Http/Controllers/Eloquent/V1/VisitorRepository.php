<?php
/**
 * Created by PhpStorm.
 * User: Al Mohands
 * Date: 22/05/2019
 * Time: 01:53 م
 */

namespace App\Http\Controllers\Eloquent\V1;

use App\Http\Controllers\Interfaces\V1\VisitorRepositoryInterface;
use App\Http\Resources\V1\CategoryResources;
use App\Http\Resources\V1\ProductResources;
use App\Http\Resources\V1\SliderResources;
use App\Http\Resources\V1\VisitorResources;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Visitor;
use JWTAuth;

class VisitorRepository implements VisitorRepositoryInterface
{

    public function check($request)
    {
        $exists_visitor = Visitor::where('unique_id', $request['unique_id'])->first();
        if ($exists_visitor) {
            $visitor = new VisitorResources($exists_visitor);

            return $visitor;
        } else {
            $visitor = Visitor::create($request);
            $visitor = new VisitorResources($visitor);
            return $visitor;
        }
    }


}
