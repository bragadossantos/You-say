<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    public function store(Request $request, Article $article)
    {
        $article->shares()->create([
            'user_id' => Auth::id(),
            'platform' => $request->get('platform', 'link'),
        ]);

        return response()->json(['total' => $article->shares()->count()]);
    }
}
