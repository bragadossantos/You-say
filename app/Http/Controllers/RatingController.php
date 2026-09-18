<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request, Article $article)
    {
        $request->validate(['stars' => 'required|integer|min:1|max:5']);

        $article->ratings()->updateOrCreate(
            ['user_id' => Auth::id()],
            ['stars' => $request->stars]
        );

        return response()->json([
            'average' => $article->averageRating(),
            'total' => $article->ratings()->count(),
        ]);
    }
}
