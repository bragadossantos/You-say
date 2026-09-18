@extends('layouts.admin')
@section('title', 'Artigos — Admin')

@section('content')
<h3 class="mb-4">Gestão de artigos</h3>

<form class="mb-3" method="GET">
    <input type="text" name="q" class="form-control" style="max-width:320px" placeholder="Pesquisar por título" value="{{ request('q') }}">
</form>

<div class="stat-card p-0">
<table class="table mb-0 align-middle">
    <thead class="table-light"><tr><th>Título</th><th>Autor</th><th>Categoria</th><th>Vistas</th><th>Estado</th><th></th></tr></thead>
    <tbody>
    @foreach ($articles as $a)
        <tr>
            <td><a href="{{ route('articles.show', $a) }}" target="_blank">{{ \Illuminate\Support\Str::limit($a->title, 45) }}</a></td>
            <td>{{ $a->user->name }}</td>
            <td>{{ $a->category->name }}</td>
            <td>{{ $a->views }}</td>
            <td>@if ($a->is_hidden)<span class="badge bg-danger">Ocultado</span>@else<span class="badge bg-success">Publicado</span>@endif</td>
            <td class="text-end">
                <form action="{{ route('admin.articles.toggleHidden', $a) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-outline-warning"><i class="bi bi-eye-slash"></i></button>
                </form>
                <form action="{{ route('admin.articles.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('Remover artigo?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
<div class="mt-3">{{ $articles->links() }}</div>
@endsection
