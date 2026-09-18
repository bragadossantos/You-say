<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withCount('articles')
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%")->orWhere('email', 'like', "%{$request->q}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggleBlock(User $user)
    {
        $user->update(['is_blocked' => ! $user->is_blocked]);

        return back()->with('status', $user->is_blocked ? 'Utilizador bloqueado.' : 'Utilizador desbloqueado.');
    }

    public function toggleAdmin(User $user)
    {
        $user->update(['role' => $user->role === 'admin' ? 'user' : 'admin']);

        return back()->with('status', 'Permissões atualizadas.');
    }

    public function destroy(User $user)
    {
        abort_if($user->isAdmin(), 403, 'Não é possível remover um administrador.');
        $user->delete();

        return back()->with('status', 'Utilizador removido.');
    }
}
