<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Traits\Message;

class DashboardController extends Controller
{

    use Message;

    public function detail()
    {

        $details = [];

        $product = Product::toBase()
        ->selectRaw("COUNT(case when active = 1 then 1 end) as active")
        ->selectRaw("COUNT(case when active = 0 then 1 end) as Inactive")
        ->first();

        $details['product'] = $product;

        $category = Category::count();

        $details['category'] = $category;

        return $this->sendResponse($details,'Data exited successfully');
    }


}
