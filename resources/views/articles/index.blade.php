@extends('layouts.app')
@section('title', ($titulo ?? 'Artigos').' — '.config('app.name'))

@section('content')
<div class="container py-5">
    <div class="section-title mt-0"><div class="bar"></div><h3>{{ $titulo ?? 'Artigos' }}</h3></div>

    <div class="row g-4">
        @forelse ($articles as $a)
            <div class="col-md-4">
                @include('partials.article-card', ['a' => $a])
            </div>
        @empty
            <p class="text-muted">Nenhum artigo encontrado.</p>
        @endforelse
    </div>

    <div class="mt-5">{{ $articles->links() }}</div>
</div>
@endsection
