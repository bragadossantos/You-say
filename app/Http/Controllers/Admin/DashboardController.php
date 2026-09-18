<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\Reaction;
use App\Models\Share;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'utilizadores' => User::count(),
            'artigos' => Article::count(),
            'comentarios' => Comment::count(),
            'likes' => Reaction::where('type', 'like')->count(),
            'dislikes' => Reaction::where('type', 'dislike')->count(),
            'avaliacoes' => Rating::count(),
            'partilhas' => Share::count(),
            'visualizacoes' => Article::sum('views'),
        ];

        $atividadeRecente = collect()
            ->concat(Article::latest()->take(5)->get()->map(fn ($a) => [
                'tipo' => 'Artigo publicado', 'descricao' => $a->title, 'data' => $a->created_at,
            ]))
            ->concat(Comment::latest()->take(5)->get()->map(fn ($c) => [
                'tipo' => 'Novo comentário', 'descricao' => \Illuminate\Support\Str::limit($c->content, 60), 'data' => $c->created_at,
            ]))
            ->concat(User::latest()->take(5)->get()->map(fn ($u) => [
                'tipo' => 'Novo utilizador', 'descricao' => $u->name, 'data' => $u->created_at,
            ]))
            ->sortByDesc('data')
            ->take(10);

        $topArtigos = Article::withCount(['reactions as likes_count' => fn ($q) => $q->where('type', 'like')])
            ->orderByDesc('views')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'atividadeRecente', 'topArtigos'));
    }
}
