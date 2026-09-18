<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    public function store(Request $request, Article $article)
    {
        $request->validate(['type' => 'required|in:like,dislike']);

        $article->reactions()->updateOrCreate(
            ['user_id' => Auth::id()],
            ['type' => $request->type]
        );

        return response()->json([
            'likes' => $article->likesCount(),
            'dislikes' => $article->dislikesCount(),
        ]);
    }

    public function destroy(Article $article)
    {
        $article->reactions()->where('user_id', Auth::id())->delete();

        return response()->json([
            'likes' => $article->likesCount(),
            'dislikes' => $article->dislikesCount(),
        ]);
    }
}
