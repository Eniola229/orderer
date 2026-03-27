<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        if (!auth('admin')->user()->canView()) abort(403);
        $brands = Brand::with('seller')
            ->when($request->search, fn($q, $s) =>
                $q->where('name', 'like', "%{$s}%")
            )
            ->latest()->paginate(20);
        return view('admin.brands.index', compact('brands'));
    }

    public function suspend(Brand $brand)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);
        $brand->update(['is_active' => false]);
        return back()->with('success', 'Brand suspended.');
    }

    public function activate(Brand $brand)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);
        $brand->update(['is_active' => true]);
        return back()->with('success', 'Brand activated.');
    }
}