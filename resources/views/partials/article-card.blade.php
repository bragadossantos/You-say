<a href="{{ route('articles.show', $a) }}" class="text-decoration-none text-dark">
    <div class="card-article">
        <img src="{{ $a->imageUrl() }}" alt="{{ $a->title }}">
        <div class="card-body">
            <span class="badge-cat">{{ $a->category->name }}</span>
            @if ($a->isThesis())
                <span class="badge-doc"><i class="bi bi-file-earmark-pdf"></i> PDF</span>
            @endif
            <h5>{{ \Illuminate\Support\Str::limit($a->title, 60) }}</h5>
            <p class="text-muted small mb-2">{{ $a->excerpt(90) }}</p>
            <div class="d-flex justify-content-between meta">
                <span><i class="bi bi-person"></i> {{ $a->user->name }}</span>
                <span>
                    <i class="bi bi-hand-thumbs-up"></i> {{ $a->likesCount() }}
                    <i class="bi bi-eye ms-2"></i> {{ $a->views }}
                </span>
            </div>
        </div>
    </div>
</a>
