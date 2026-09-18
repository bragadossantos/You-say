@extends('layouts.admin')
@section('title', 'Comentários — Admin')

@section('content')
<h3 class="mb-4">Moderação de comentários</h3>

<form class="mb-3" method="GET">
    <input type="text" name="q" class="form-control" style="max-width:320px" placeholder="Pesquisar comentário" value="{{ request('q') }}">
</form>

<div class="stat-card p-0">
<table class="table mb-0 align-middle">
    <thead class="table-light"><tr><th>Comentário</th><th>Utilizador</th><th>Artigo</th><th>Estado</th><th></th></tr></thead>
    <tbody>
    @foreach ($comments as $c)
        <tr>
            <td>{{ \Illuminate\Support\Str::limit($c->content, 60) }}</td>
            <td>{{ $c->user->name }}</td>
            <td><a href="{{ route('articles.show', $c->article) }}" target="_blank">{{ \Illuminate\Support\Str::limit($c->article->title, 30) }}</a></td>
            <td>@if ($c->is_hidden)<span class="badge bg-danger">Ocultado</span>@else<span class="badge bg-success">Visível</span>@endif</td>
            <td class="text-end">
                <form action="{{ route('admin.comments.toggleHidden', $c) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-outline-warning"><i class="bi bi-eye-slash"></i></button>
                </form>
                <form action="{{ route('admin.comments.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Remover comentário?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
<div class="mt-3">{{ $comments->links() }}</div>
@endsection
