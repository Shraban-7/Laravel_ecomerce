<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ProductSubCategoryController extends Controller
{
    public function index(Request $request){
        if (!empty($request->category_id)) {
            $sub_categories = SubCategory::where('category_id',$request->category_id)
            ->orderBy('name', 'asc')->get();

            return response()->json([
                'status'=>true,
                'sub_categories'=>$sub_categories
            ]);
        }
        else{
            return response()->json([
                'status'=>true,
                'sub_categories'=>[]
            ]);
        }
    }
}
