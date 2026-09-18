<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('articles')->orderBy('name')->get();

        $recentes = Article::with(['user', 'category'])
            ->where('is_hidden', false)
            ->latest('published_at')
            ->take(6)->get();

        $populares = Article::with(['user', 'category'])
            ->where('is_hidden', false)
            ->withCount(['reactions as likes_count' => fn ($q) => $q->where('type', 'like')])
            ->orderByDesc('likes_count')
            ->orderByDesc('views')
            ->take(6)->get();

        $destaque = Article::with(['user', 'category'])
            ->where('is_hidden', false)
            ->latest('published_at')
            ->take(5)->get();

        return view('home', compact('categories', 'recentes', 'populares', 'destaque'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q');

        $artigos = Article::with(['user', 'category'])
            ->where('is_hidden', false)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('content', 'like', "%{$q}%");
                });
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('articles.index', ['articles' => $artigos, 'titulo' => 'Resultados para "'.$q.'"']);
    }
}
