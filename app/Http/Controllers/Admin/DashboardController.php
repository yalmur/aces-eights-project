<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $todayStats = Order::whereDate('created_at', today())
            ->whereNotIn('status', ['pending_payment', 'cancelled'])
            ->selectRaw('COUNT(*) as order_count, COALESCE(SUM(total), 0) as revenue')
            ->first();

        $todayOrders  = (int) $todayStats->order_count;
        $todayRevenue = (float) $todayStats->revenue;

        $yesterdayOrders = Order::whereDate('created_at', today()->subDay())
            ->whereNotIn('status', ['pending_payment', 'cancelled'])
            ->count();

        $orderGrowth = $yesterdayOrders > 0
            ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100)
            : null;

        $activeDeliveries = Order::where('status', 'out_for_delivery')->count();

        $kitchenQueue = Order::whereIn('status', ['accepted', 'cooking'])
            ->whereDate('created_at', today())
            ->count();

        $kitchenLoad = match(true) {
            $kitchenQueue >= 8 => 'HIGH',
            $kitchenQueue >= 4 => 'MODERATE',
            default            => 'LOW',
        };

        $recentOrders = Order::with('items')
            ->whereNotIn('status', ['pending_payment', 'cancelled', 'delivered', 'collected'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'title'          => 'Dashboard',
            'todayOrders'    => $todayOrders,
            'todayRevenue'   => $todayRevenue,
            'orderGrowth'    => $orderGrowth,
            'activeDeliveries' => $activeDeliveries,
            'kitchenLoad'    => $kitchenLoad,
            'kitchenQueue'   => $kitchenQueue,
            'recentOrders'   => $recentOrders,
        ]);
    }
}
