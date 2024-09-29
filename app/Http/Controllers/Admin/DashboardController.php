<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\TemporaryImages;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrder = Order::where('status', '!=', 'cancelled')->count();
        $totalProduct = Product::count();
        $totalCustomer = User::where('role', 1)->count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('grand_total');

        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $nameOfLastMonth = Carbon::now()->subMonth()->startOfMonth()->format('F');
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        $last30DayStartDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $currentDate = Carbon::now()->format('Y-m-d');
        $revenueOfThisMonth = Order::where('status', '!=', 'cancelled')->whereDate('created_at', '>=', $startOfMonth)->whereDate('created_at', '<=', $currentDate)->sum('grand_total');
        $revenueOfLastMonth = Order::where('status', '!=', 'cancelled')->whereDate('created_at', '>=', $startOfLastMonth)->whereDate('created_at', '<=', $endOfLastMonth)->sum('grand_total');
        $revenueOfLast30Days = Order::where('status', '!=', 'cancelled')->whereDate('created_at', '>=', $last30DayStartDate)->whereDate('created_at', '<=', $currentDate)->sum('grand_total');

        //Delete temporary image
        $dayBeforeToday = Carbon::now()->subDays(1)->format('Y-m-d');
        $temp_images = TemporaryImages::where('created_at', '<=', $dayBeforeToday)->get();

        foreach ($temp_images as $key => $value) {

            $path = public_path('/temp/' . $value->name);

            $thumb_path = public_path('/temp/thumbnail/' . 'thumb_'.$value->name);

            if (File::exists($path)) {
                File::delete($path);
            }

            if (File::exists($thumb_path)) {
                File::delete($thumb_path);
            }


        }

        return view('admin.dashboard', [
            'total_order' => $totalOrder,
            'total_product' => $totalProduct,
            'total_customer' => $totalCustomer,
            'total_revenue' => $totalRevenue,
            'total_revenue_this_month' => $revenueOfThisMonth,
            'total_revenue_last_month' => $revenueOfLastMonth,
            'total_revenue_last_30_days' => $revenueOfLast30Days,
            'last_month_name' => $nameOfLastMonth,
        ]);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
