@extends('layouts.admin')

@section('content')
<main class="flex-1 p-6">
    <header class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-1">Shop Orders</h2>
            <p class="text-sm text-gray-500">Customer name, email, and country collected at checkout.</p>
        </div>
    </header>

    <form method="GET" action="{{ route('admin.shop-orders.index') }}" class="mb-6 flex flex-col sm:flex-row gap-3">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search name, email, country..."
            class="w-full sm:max-w-xs rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
        <select name="status" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-700">
            <option value="">All statuses</option>
            <option value="paid" @selected($status === 'paid')>Paid</option>
            <option value="pending" @selected($status === 'pending')>Pending</option>
        </select>
        <button type="submit" class="bg-gray-900 hover:bg-black text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
            Filter
        </button>
    </form>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Country</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Payment</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $order->full_name }}</div>
                                @if($order->order_number)
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $order->order_number }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                <a href="mailto:{{ $order->email }}" class="hover:underline">{{ $order->email }}</a>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $order->country }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                ${{ number_format((float) $order->total, 2) }}
                                <span class="text-xs text-gray-400">{{ $order->currency }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 capitalize">{{ $order->payment_method ?: '—' }}</td>
                            <td class="px-6 py-4">
                                @if($order->status === 'paid')
                                    <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                {{ $order->created_at?->format('M j, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.shop-orders.show', $order) }}" class="text-sm font-semibold text-blue-600 hover:underline">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                No checkout customers yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</main>
@endsection
