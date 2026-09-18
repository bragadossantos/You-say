@extends('layouts.app')
@section('title', 'Os meus artigos — '.config('app.name'))

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h3 class="mb-0">Os meus artigos</h3>
        <a href="{{ route('articles.create') }}" class="btn btn-orange"><i class="bi bi-plus-lg"></i> Novo artigo</a>
    </div>

    <div class="row g-4">
        @forelse ($articles as $a)
            <div class="col-md-4">
                <div class="card-article position-relative">
                    <img src="{{ $a->imageUrl() }}" alt="{{ $a->title }}">
                    <div class="card-body">
                        <span class="badge-cat">{{ $a->category->name }}</span>
                        @if ($a->is_hidden)
                            <span class="badge bg-danger ms-1">Ocultado pelo admin</span>
                        @endif
                        <h5>{{ \Illuminate\Support\Str::limit($a->title, 55) }}</h5>
                        <p class="text-muted small mb-2">Publicado em {{ $a->published_at->format('d/m/Y') }} &middot; {{ $a->views }} vistas</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('articles.show', $a) }}" class="btn btn-sm btn-outline-orange flex-fill">Ver</a>
                            <a href="{{ route('articles.edit', $a) }}" class="btn btn-sm btn-outline-secondary flex-fill">Editar</a>
                            <form action="{{ route('articles.destroy', $a) }}" method="POST" onsubmit="return confirm('Eliminar este artigo?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Ainda não publicou nenhum artigo. <a href="{{ route('articles.create') }}">Publique o primeiro agora</a>.</p>
        @endforelse
    </div>

    <div class="mt-5">{{ $articles->links() }}</div>
</div>
@endsection
