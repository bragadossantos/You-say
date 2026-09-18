<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function category(Category $category)
    {
        $articles = Article::with(['user', 'category'])
            ->where('category_id', $category->id)
            ->where('is_hidden', false)
            ->latest('published_at')
            ->paginate(9);

        return view('articles.index', ['articles' => $articles, 'titulo' => 'Categoria: '.$category->name]);
    }

    public function my()
    {
        $articles = Auth::user()->articles()->with('category')->latest()->paginate(9);

        return view('articles.my', compact('articles'));
    }

    public function show(Article $article)
    {
        abort_if($article->is_hidden && (! Auth::check() || (! Auth::user()->isAdmin() && Auth::id() !== $article->user_id)), 404);

        $article->increment('views');
        $article->load(['user', 'category', 'comments.user']);

        $userReaction = Auth::check() ? $article->reactions()->where('user_id', Auth::id())->value('type') : null;
        $userRating = Auth::check() ? $article->ratings()->where('user_id', Auth::id())->value('stars') : null;

        $relacionados = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->where('is_hidden', false)
            ->take(3)->get();

        return view('articles.show', compact('article', 'userReaction', 'userRating', 'relacionados'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'image' => 'nullable|image|max:4096',
            'published_at' => 'nullable|date',
        ]);

        $data['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article = Article::create($data);

        return redirect()->route('articles.show', $article)->with('status', 'Artigo publicado com sucesso!');
    }

    public function edit(Article $article)
    {
        $this->authorizeOwner($article);
        $categories = Category::orderBy('name')->get();

        return view('articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $this->authorizeOwner($article);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'image' => 'nullable|image|max:4096',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($data);

        return redirect()->route('articles.show', $article)->with('status', 'Artigo atualizado com sucesso!');
    }

    public function destroy(Article $article)
    {
        $this->authorizeOwner($article);

        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }
        $article->delete();

        return redirect()->route('articles.my')->with('status', 'Artigo eliminado.');
    }

    private function authorizeOwner(Article $article): void
    {
        abort_unless(Auth::id() === $article->user_id || Auth::user()?->isAdmin(), 403, 'Não tem permissão para editar este artigo.');
    }
}
