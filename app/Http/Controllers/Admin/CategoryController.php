<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        if (!auth('admin')->user()->canManageCategories()) abort(403);
        $categories = Category::with('subcategories')->withCount(['products', 'subcategories'])->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!auth('admin')->user()->canManageCategories()) abort(403);

        $request->validate([
            'name'            => ['required', 'string', 'max:100'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'icon'            => ['nullable', 'string'],
        ]);

        Category::create([
            'name'            => $request->name,
            'slug'            => Str::slug($request->name),
            'commission_rate' => $request->commission_rate ?? 10,
            'icon'            => $request->icon,
            'is_active'       => true,
        ]);

        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, Category $category)
    {
        if (!auth('admin')->user()->canManageCategories()) abort(403);

        $request->validate([
            'name'            => ['required', 'string', 'max:100'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'icon' => ['nullable', 'required'],
        ]);

        $category->update([
            'name'            => $request->name,
            'commission_rate' => $request->commission_rate ?? $category->commission_rate,
            'icon' => $request->icon,
            'is_active'       => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Category updated.');
    }

    public function storeSubcategory(Request $request, Category $category)
    {
        if (!auth('admin')->user()->canManageCategories()) abort(403);

        $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) use ($category) {
                    $exists = Subcategory::where('name', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Subcategory "' . $value . '" already exists under a category.');
                    }
                }
            ]
        ]);

        Subcategory::create([
            'category_id' => $category->id,
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'is_active'   => true,
        ]);

        return back()->with('success', 'Subcategory added.');
    }
}