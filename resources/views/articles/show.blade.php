@extends('layouts.app')
@section('title', $article->title.' — '.config('app.name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <span class="badge-cat mb-2 d-inline-block">{{ $article->category->name }}</span>
            <h1 class="mb-3">{{ $article->title }}</h1>

            <div class="d-flex align-items-center gap-3 flex-wrap mb-4 text-muted small">
                <span><i class="bi bi-person-circle"></i> {{ $article->user->name }}</span>
                <span><i class="bi bi-calendar3"></i> {{ $article->published_at?->format('d/m/Y') }}</span>
                <span><i class="bi bi-eye"></i> {{ $article->views }} vistas</span>
                <span><i class="bi bi-star-fill text-warning"></i> <span id="rating-average">{{ $article->averageRating() }}</span> ({{ $article->ratings()->count() }} <span id="rating-total">{{ $article->ratings()->count() }}</span> avaliações)</span>

                @if (Auth::id() === $article->user_id || Auth::user()?->isAdmin())
                    <a href="{{ route('articles.edit', $article) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i> Editar</a>
                @endif
            </div>

            <div class="article-hero mb-4">
                <img src="{{ $article->imageUrl() }}" alt="{{ $article->title }}">
            </div>

            @if ($article->isThesis())
                <div class="thesis-meta mb-4 p-4 rounded-4">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6 col-lg-4">
                            <div class="text-muted small">Autor</div>
                            <div class="fw-semibold">{{ $article->author_name }}</div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="text-muted small">Universidade</div>
                            <div class="fw-semibold">{{ $article->institution }}</div>
                        </div>
                        @if ($article->course)
                            <div class="col-sm-6 col-lg-4">
                                <div class="text-muted small">Curso</div>
                                <div class="fw-semibold">{{ $article->course }}</div>
                            </div>
                        @endif
                        @if ($article->academic_level)
                            <div class="col-sm-6 col-lg-4">
                                <div class="text-muted small">Nível académico</div>
                                <div class="fw-semibold">{{ $article->academic_level }}</div>
                            </div>
                        @endif
                        @if ($article->completion_year)
                            <div class="col-sm-6 col-lg-4">
                                <div class="text-muted small">Ano de conclusão</div>
                                <div class="fw-semibold">{{ $article->completion_year }}</div>
                            </div>
                        @endif
                        @if ($article->country)
                            <div class="col-sm-6 col-lg-4">
                                <div class="text-muted small">País de origem</div>
                                <div class="fw-semibold">{{ $article->country }}</div>
                            </div>
                        @endif
                    </div>
                    @if ($article->document_path)
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('articles.document.view', $article) }}" target="_blank" rel="noopener" class="btn btn-outline-orange">
                                <i class="bi bi-eye"></i> Abrir PDF
                            </a>
                            <a href="{{ route('articles.document.download', $article) }}" class="btn btn-orange">
                                <i class="bi bi-download"></i> Descarregar PDF
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <div class="article-content mb-5">
                {!! nl2br(e($article->content)) !!}
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-4 border-top border-bottom mb-4">
                <div class="d-flex gap-2">
                    <button class="reaction-btn {{ $userReaction === 'like' ? 'active-like' : '' }}" data-reaction="like" data-url="{{ route('reactions.store', $article) }}">
                        <i class="bi bi-hand-thumbs-up"></i> <span id="likes-count">{{ $article->likesCount() }}</span>
                    </button>
                    <button class="reaction-btn {{ $userReaction === 'dislike' ? 'active-dislike' : '' }}" data-reaction="dislike" data-url="{{ route('reactions.store', $article) }}">
                        <i class="bi bi-hand-thumbs-down"></i> <span id="dislikes-count">{{ $article->dislikesCount() }}</span>
                    </button>
                </div>

                <div class="star-rating" data-url="{{ route('ratings.store', $article) }}">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="bi {{ $i <= ($userRating ?? 0) ? 'bi-star-fill active' : 'bi-star' }}" data-value="{{ $i }}"></i>
                    @endfor
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-orange" data-share="whatsapp" data-register-url="{{ route('shares.store', $article) }}"><i class="bi bi-whatsapp"></i></button>
                    <button class="btn btn-sm btn-outline-orange" data-share="facebook" data-register-url="{{ route('shares.store', $article) }}"><i class="bi bi-facebook"></i></button>
                    <button class="btn btn-sm btn-outline-orange" data-share="x" data-register-url="{{ route('shares.store', $article) }}"><i class="bi bi-twitter-x"></i></button>
                    <button class="btn btn-sm btn-outline-orange" data-share="link" data-register-url="{{ route('shares.store', $article) }}"><i class="bi bi-link-45deg"></i> Copiar</button>
                </div>
            </div>

            <div class="mb-5">
                <h4 class="mb-3"><i class="bi bi-chat-dots"></i> Comentários ({{ $article->comments->count() }})</h4>

                @auth
                    <form action="{{ route('comments.store', $article) }}" method="POST" class="mb-4">
                        @csrf
                        <textarea name="content" class="form-control mb-2" rows="3" placeholder="Escreva um comentário..." required></textarea>
                        <button class="btn btn-orange btn-sm">Comentar</button>
                    </form>
                @else
                    <p class="text-muted"><a href="{{ route('login') }}">Inicie sessão</a> para comentar.</p>
                @endauth

                <div class="d-flex flex-column gap-3">
                    @forelse ($article->comments as $c)
                        <div class="comment-box">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $c->user->name }}</strong>
                                <span class="text-muted small">{{ $c->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mb-1 mt-1">{{ $c->content }}</p>
                            @if (Auth::id() === $c->user_id || Auth::user()?->isAdmin())
                                <form action="{{ route('comments.destroy', [$article, $c]) }}" method="POST" onsubmit="return confirm('Remover comentário?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0">Remover</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted">Seja o primeiro a comentar.</p>
                    @endforelse
                </div>
            </div>

            @if ($relacionados->isNotEmpty())
                <div class="section-title"><div class="bar"></div><h3>Artigos relacionados</h3></div>
                <div class="row g-4">
                    @foreach ($relacionados as $r)
                        <div class="col-md-4">@include('partials.article-card', ['a' => $r])</div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
