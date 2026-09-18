@extends('layouts.admin')
@section('title', 'Utilizadores — Admin')

@section('content')
<h3 class="mb-4">Gestão de utilizadores</h3>

<form class="mb-3" method="GET">
    <input type="text" name="q" class="form-control" style="max-width:320px" placeholder="Pesquisar por nome ou e-mail" value="{{ request('q') }}">
</form>

<div class="stat-card p-0">
<table class="table mb-0 align-middle">
    <thead class="table-light"><tr><th>Nome</th><th>E-mail</th><th>Artigos</th><th>Função</th><th>Estado</th><th></th></tr></thead>
    <tbody>
    @foreach ($users as $u)
        <tr>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->articles_count }}</td>
            <td><span class="badge {{ $u->isAdmin() ? 'bg-primary' : 'bg-secondary' }}">{{ $u->role }}</span></td>
            <td>@if ($u->is_blocked)<span class="badge bg-danger">Bloqueado</span>@else<span class="badge bg-success">Ativo</span>@endif</td>
            <td class="text-end">
                <form action="{{ route('admin.users.toggleAdmin', $u) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-outline-primary" title="Tornar admin/utilizador"><i class="bi bi-shield"></i></button>
                </form>
                <form action="{{ route('admin.users.toggleBlock', $u) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-outline-warning" title="Bloquear/Desbloquear"><i class="bi bi-slash-circle"></i></button>
                </form>
                @if (!$u->isAdmin())
                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Remover utilizador?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
