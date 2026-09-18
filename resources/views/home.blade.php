@extends('layouts.app')
@section('title', config('app.name').' — Publica e descobre artigos')

@section('content')

<div class="hero-carousel">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            @foreach ($destaque as $i => $a)
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @forelse ($destaque as $i => $a)
                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                    <a href="{{ route('articles.show', $a) }}" class="hero-slide" style="background-image:url('{{ $a->imageUrl() }}')">
                        <div class="hero-caption">
                            <span class="badge-cat">{{ $a->category->name }}</span>
                            <h2>{{ $a->title }}</h2>
                            <p>{{ $a->excerpt(120) }}</p>
                            <span class="text-white small"><i class="bi bi-person-circle"></i> {{ $a->user->name }} &middot; {{ $a->published_at->format('d/m/Y') }}</span>
                        </div>
                    </a>
                </div>
            @empty
                <div class="carousel-item active">
                    <div class="hero-slide" style="background-image:url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1400&q=80')">
                        <div class="hero-caption">
                            <h2>Bem-vindo à ArtigosUGS</h2>
                            <p>Seja o primeiro a publicar um artigo na plataforma.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<div class="container">

    <div class="d-flex flex-wrap gap-2 justify-content-center py-4">
        @foreach ($categories as $cat)
            <a href="{{ route('articles.category', $cat) }}" class="category-pill">
                <i class="bi {{ $cat->icon ?? 'bi-tag' }}"></i> {{ $cat->name }}
                <span class="text-muted">({{ $cat->articles_count }})</span>
            </a>
        @endforeach
    </div>

    <div class="section-title"><div class="bar"></div><h3>Artigos recentes</h3></div>
    <div class="row g-4">
        @forelse ($recentes as $a)
            <div class="col-md-4">
                @include('partials.article-card', ['a' => $a])
            </div>
        @empty
            <p class="text-muted">Ainda não existem artigos publicados.</p>
        @endforelse
    </div>

    <div class="section-title"><div class="bar"></div><h3>Mais populares</h3></div>
    <div class="row g-4 mb-5">
        @forelse ($populares as $a)
            <div class="col-md-4">
                @include('partials.article-card', ['a' => $a])
            </div>
        @empty
            <p class="text-muted">Ainda não existem artigos populares.</p>
        @endforelse
    </div>

</div>
@endsection
