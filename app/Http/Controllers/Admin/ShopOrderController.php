<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use Illuminate\Http\Request;

class ShopOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = ShopOrder::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->q.'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('country', 'like', $term)
                        ->orWhere('order_number', 'like', $term);
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.shop_orders.index', [
            'orders' => $orders,
            'status' => $status,
            'q' => $request->q,
        ]);
    }

    public function show(ShopOrder $shopOrder)
    {
        return view('admin.shop_orders.show', [
            'order' => $shopOrder,
        ]);
    }
}
