<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Article $article)
    {
        $request->validate(['content' => 'required|string|max:2000']);

        $article->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return back()->with('status', 'Comentário publicado.');
    }

    public function destroy(Article $article, \App\Models\Comment $comment)
    {
        abort_unless(Auth::id() === $comment->user_id || Auth::user()?->isAdmin(), 403);
        $comment->delete();

        return back()->with('status', 'Comentário removido.');
    }
}
