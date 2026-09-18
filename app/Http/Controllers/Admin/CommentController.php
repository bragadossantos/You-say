<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with(['user', 'article'])
            ->when($request->q, fn ($q) => $q->where('content', 'like', "%{$request->q}%"))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    public function toggleHidden(Comment $comment)
    {
        $comment->update(['is_hidden' => ! $comment->is_hidden]);

        return back()->with('status', 'Estado do comentário atualizado.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with('status', 'Comentário removido.');
    }
}
