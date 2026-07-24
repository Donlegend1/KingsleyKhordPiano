<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopProductController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'midi');
        $type = in_array($type, ['midi', 'plugin']) ? $type : 'midi';

        $products = ShopProduct::where('type', $type)->latest()->get();

        return view('admin.shop.index', [
            'products' => $products,
            'type' => $type,
        ]);
    }

    public function create(Request $request)
    {
        $type = in_array($request->query('type'), ['midi', 'plugin']) ? $request->query('type') : 'midi';

        return view('admin.shop.create', ['type' => $type]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $this->storeThumbnail($request);
        }

        $validated['slug'] = $this->uniqueSlug($validated['title']);

        ShopProduct::create($validated);

        return redirect('/admin/shop?type=' . $validated['type'])
            ->with('success', 'Product created.');
    }

    public function edit(ShopProduct $shopProduct)
    {
        return view('admin.shop.edit', ['product' => $shopProduct]);
    }

    public function update(Request $request, ShopProduct $shopProduct)
    {
        $validated = $this->validateProduct($request, $shopProduct->id);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $this->storeThumbnail($request);
        }

        if ($validated['title'] !== $shopProduct->title) {
            $validated['slug'] = $this->uniqueSlug($validated['title'], $shopProduct->id);
        }

        $shopProduct->update($validated);

        return redirect('/admin/shop?type=' . $shopProduct->type)
            ->with('success', 'Product updated.');
    }

    public function destroy(ShopProduct $shopProduct)
    {
        $type = $shopProduct->type;
        $shopProduct->delete();

        return redirect('/admin/shop?type=' . $type)
            ->with('success', 'Product deleted.');
    }

    private function validateProduct(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'type' => 'required|in:midi,plugin',
            'title' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:regular_price',
            'download_url' => 'nullable|url|max:2048',
            'video_url' => 'nullable|url|max:2048',
            'system_requirements' => 'nullable|string',
        ]);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (
            ShopProduct::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . ++$i;
        }

        return $slug;
    }

    private function storeThumbnail(Request $request): string
    {
        $file = $request->file('thumbnail');
        $filename = time() . '_' . $file->getClientOriginalName();

        $destination = base_path('../public_html/uploads/shop');
        if (!file_exists($destination)) {
            $destination = public_path('uploads/shop');
        }
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);

        return 'uploads/shop/' . $filename;
    }
}
