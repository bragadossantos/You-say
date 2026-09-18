<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with(['user', 'category'])
            ->when($request->q, fn ($q) => $q->where('title', 'like', "%{$request->q}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.articles.index', compact('articles'));
    }

    public function toggleHidden(Article $article)
    {
        $article->update(['is_hidden' => ! $article->is_hidden]);

        return back()->with('status', $article->is_hidden ? 'Artigo ocultado.' : 'Artigo publicado novamente.');
    }

    public function destroy(Article $article)
    {
        if ($article->image) {
            \Storage::disk('public')->delete($article->image);
        }
        $article->delete();

        return back()->with('status', 'Artigo removido.');
    }
}
