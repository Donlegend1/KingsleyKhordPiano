@extends('layouts.admin')

@section('content')
<main class="flex-1 p-6">
    <header class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.shop-orders.index') }}" class="text-sm text-gray-500 hover:text-gray-800">&larr; Back to orders</a>
            <h2 class="text-xl font-bold text-gray-800 mt-2">Order Details</h2>
            <p class="text-sm text-gray-500">{{ $order->order_number ?: 'Pending checkout' }}</p>
        </div>
        @if($order->status === 'paid')
            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
        @else
            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
        @endif
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-white border border-gray-200 rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">Customer</h3>
            <div>
                <p class="text-xs text-gray-400">Name</p>
                <p class="font-semibold text-gray-900">{{ $order->full_name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Email</p>
                <a href="mailto:{{ $order->email }}" class="font-semibold text-blue-600 hover:underline">{{ $order->email }}</a>
            </div>
            <div>
                <p class="text-xs text-gray-400">Country</p>
                <p class="font-semibold text-gray-900">{{ $order->country }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Payment Method</p>
                <p class="font-semibold text-gray-900 capitalize">{{ $order->payment_method ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Payment Reference</p>
                <p class="font-mono text-xs text-gray-700 break-all">{{ $order->payment_reference ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Submitted</p>
                <p class="text-sm text-gray-700">{{ $order->created_at?->format('M j, Y g:i A') }}</p>
            </div>
            @if($order->paid_at)
                <div>
                    <p class="text-xs text-gray-400">Paid At</p>
                    <p class="text-sm text-gray-700">{{ $order->paid_at->format('M j, Y g:i A') }}</p>
                </div>
            @endif
        </div>

        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">Items</h3>
                <p class="text-lg font-extrabold text-gray-900">${{ number_format((float) $order->total, 2) }} {{ $order->currency }}</p>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse(($order->items ?? []) as $item)
                    <div class="flex items-center justify-between py-3 gap-4">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $item['name'] ?? $item['slug'] ?? 'Item' }}</p>
                            <p class="text-xs text-gray-500">{{ $item['type'] ?? '' }} · Qty {{ $item['qty'] ?? 1 }}</p>
                        </div>
                        <p class="font-semibold text-gray-900">
                            ${{ number_format((float) (($item['price'] ?? 0) * ($item['qty'] ?? 1)), 2) }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-6 text-center">No items recorded.</p>
                @endforelse
            </div>
        </div>
    </div>
</main>
@endsection
