@extends('layouts.admin')
@section('title', 'Dashboard — Admin')

@section('content')
<h3 class="mb-4">Dashboard</h3>

<div class="row g-3 mb-4">
    @php
        $cards = [
            ['label' => 'Utilizadores', 'value' => $stats['utilizadores'], 'icon' => 'bi-people'],
            ['label' => 'Artigos', 'value' => $stats['artigos'], 'icon' => 'bi-journal-text'],
            ['label' => 'Comentários', 'value' => $stats['comentarios'], 'icon' => 'bi-chat-dots'],
            ['label' => 'Visualizações', 'value' => $stats['visualizacoes'], 'icon' => 'bi-eye'],
            ['label' => 'Likes', 'value' => $stats['likes'], 'icon' => 'bi-hand-thumbs-up'],
            ['label' => 'Dislikes', 'value' => $stats['dislikes'], 'icon' => 'bi-hand-thumbs-down'],
            ['label' => 'Avaliações', 'value' => $stats['avaliacoes'], 'icon' => 'bi-star'],
            ['label' => 'Partilhas', 'value' => $stats['partilhas'], 'icon' => 'bi-share'],
        ];
    @endphp
    @foreach ($cards as $c)
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon"><i class="bi {{ $c['icon'] }}"></i></div>
                <div>
                    <div class="stat-number">{{ $c['value'] }}</div>
                    <div class="text-muted small">{{ $c['label'] }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="stat-card">
            <h5 class="mb-3">Atividade recente</h5>
            <ul class="list-group list-group-flush">
                @forelse ($atividadeRecente as $a)
                    <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                        <div>
                            <span class="badge bg-secondary">{{ $a['tipo'] }}</span>
                            <div class="small mt-1">{{ $a['descricao'] }}</div>
                        </div>
                        <span class="text-muted small">{{ \Carbon\Carbon::parse($a['data'])->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="list-group-item px-0 text-muted">Sem atividade recente.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="stat-card">
            <h5 class="mb-3">Top artigos (visualizações)</h5>
            <ul class="list-group list-group-flush">
                @forelse ($topArtigos as $t)
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>{{ \Illuminate\Support\Str::limit($t->title, 35) }}</span>
                        <span class="text-muted small"><i class="bi bi-eye"></i> {{ $t->views }} &middot; <i class="bi bi-hand-thumbs-up"></i> {{ $t->likes_count }}</span>
                    </li>
                @empty
                    <li class="list-group-item px-0 text-muted">Sem dados.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
