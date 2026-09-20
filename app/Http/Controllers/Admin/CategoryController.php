<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('articles')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'icon' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:article,thesis',
        ]);
        $data['slug'] = Str::slug($data['name']);
        $data['type'] = $data['type'] ?? 'article';

        Category::create($data);

        return back()->with('status', 'Categoria criada.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'icon' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:article,thesis',
        ]);
        $data['slug'] = Str::slug($data['name']);
        $data['type'] = $data['type'] ?? 'article';

        $category->update($data);

        return back()->with('status', 'Categoria atualizada.');
    }

    public function destroy(Category $category)
    {
        if ($category->articles()->exists()) {
            return back()->withErrors('Não é possível remover uma categoria com artigos associados.');
        }
        $category->delete();

        return back()->with('status', 'Categoria removida.');
    }
}
