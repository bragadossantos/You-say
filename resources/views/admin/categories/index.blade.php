@extends('layouts.admin')
@section('title', 'Categorias — Admin')

@section('content')
<h3 class="mb-4">Gestão de categorias</h3>

<div class="row">
    <div class="col-lg-4">
        <div class="stat-card mb-4">
            <h5 class="mb-3">Nova categoria</h5>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="mb-2">
                    <input type="text" name="name" class="form-control" placeholder="Nome da categoria" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="icon" class="form-control" placeholder="Ícone (ex: bi-cpu) — opcional">
                </div>
                <button class="btn btn-orange w-100">Adicionar</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="stat-card p-0">
            <table class="table mb-0 align-middle">
                <thead class="table-light"><tr><th>Nome</th><th>Artigos</th><th></th></tr></thead>
                <tbody>
                @foreach ($categories as $cat)
                    <tr>
                        <td><i class="bi {{ $cat->icon ?? 'bi-tag' }}"></i> {{ $cat->name }}</td>
                        <td>{{ $cat->articles_count }}</td>
                        <td class="text-end">
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Remover categoria?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
