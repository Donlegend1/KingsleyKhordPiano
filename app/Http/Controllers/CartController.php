<?php

namespace App\Http\Controllers;

use App\Support\ShopCatalog;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Session cart shape: ['slug' => qty, ...]. Full product details are always
     * re-hydrated from the catalog rather than duplicated into the session.
     */
    public static function hydrate(): array
    {
        $cart = session('cart', []);
        $catalog = ShopCatalog::all();

        $items = [];
        foreach ($cart as $slug => $qty) {
            if (!isset($catalog[$slug]) || $qty < 1) {
                continue;
            }

            $product = $catalog[$slug];
            $items[] = [
                'slug' => $slug,
                'name' => $product['name'],
                'type' => $product['type'] === 'plugin' ? 'Plugin' : 'MIDI File',
                'price' => $product['price'],
                'qty' => $qty,
                'thumbnail' => $product['thumbnail'],
                'from' => $product['from'],
                'to' => $product['to'],
            ];
        }

        return $items;
    }

    public static function count(): int
    {
        return count(session('cart', []));
    }

    public function index()
    {
        $items = static::hydrate();
        $subtotal = collect($items)->sum(fn ($i) => $i['price'] * $i['qty']);

        $related = collect(ShopCatalog::all())
            ->reject(fn ($p) => isset(session('cart', [])[$p['slug']]))
            ->take(4)
            ->map(fn ($p) => [
                'slug' => $p['slug'],
                'name' => $p['name'],
                'type' => $p['type'] === 'plugin' ? 'Plugin' : 'MIDI File',
                'label' => $p['label'],
                'price' => $p['price'],
                'thumbnail' => $p['thumbnail'],
                'from' => $p['from'],
                'to' => $p['to'],
            ])
            ->values()
            ->all();

        return view('cart', [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => 0,
            'total' => $subtotal,
            'related' => $related,
            'cartCount' => static::count(),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate(['slug' => 'required|string']);

        $product = ShopCatalog::find($request->slug);
        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        $cart = session('cart', []);
        $cart[$request->slug] = ($cart[$request->slug] ?? 0) + 1;
        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'cartCount' => static::count(),
            'product' => $product['name'],
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate(['slug' => 'required|string']);

        $cart = session('cart', []);
        unset($cart[$request->slug]);
        session(['cart' => $cart]);

        return response()->json(['success' => true, 'cartCount' => static::count()]);
    }

    public function updateQty(Request $request)
    {
        $request->validate([
            'slug' => 'required|string',
            'qty' => 'required|integer|min:0',
        ]);

        $cart = session('cart', []);

        if ($request->qty < 1) {
            unset($cart[$request->slug]);
        } else {
            $cart[$request->slug] = $request->qty;
        }

        session(['cart' => $cart]);

        return response()->json(['success' => true, 'cartCount' => static::count()]);
    }

    public function clear()
    {
        session(['cart' => []]);

        return response()->json(['success' => true, 'cartCount' => 0]);
    }
}
