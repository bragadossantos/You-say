@extends('layouts.app')
@section('title', 'Editar artigo — '.config('app.name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="section-title mt-0"><div class="bar"></div><h3>Editar artigo</h3></div>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-4 mb-4">
                    <strong><i class="bi bi-exclamation-triangle"></i> Corrija os seguintes erros:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 p-md-5 rounded-4 shadow-sm">
                @csrf @method('PUT')

                {{-- Title --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold"><i class="bi bi-type-h1"></i> Título do artigo</label>
                    <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror"
                           value="{{ old('title', $article->title) }}" required maxlength="255">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Category + Date --}}
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold"><i class="bi bi-tag"></i> Categoria</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $article->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold"><i class="bi bi-calendar3"></i> Data de publicação</label>
                        <input type="date" name="published_at" class="form-control @error('published_at') is-invalid @enderror"
                               value="{{ old('published_at', $article->published_at?->format('Y-m-d')) }}">
                        @error('published_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Current Image + New Upload --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold"><i class="bi bi-image"></i> Imagem de capa</label>
                    @if ($article->image)
                        <div class="mb-3 position-relative" style="max-height:250px; overflow:hidden; border-radius: var(--radius);">
                            <img src="{{ $article->imageUrl() }}" class="w-100" style="object-fit:cover; max-height:250px;">
                            <span class="badge bg-success position-absolute top-0 start-0 m-2">Imagem atual</span>
                        </div>
                    @endif
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    <div class="form-text">Selecione uma nova imagem para substituir a atual (opcional). Máximo 4MB.</div>
                    @error('image')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Content --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold"><i class="bi bi-text-paragraph"></i> Conteúdo do artigo</label>
                    <div class="editor-toolbar">
                        <button type="button" class="toolbar-btn" data-action="bold" title="Negrito"><i class="bi bi-type-bold"></i></button>
                        <button type="button" class="toolbar-btn" data-action="italic" title="Itálico"><i class="bi bi-type-italic"></i></button>
                        <button type="button" class="toolbar-btn" data-action="underline" title="Sublinhado"><i class="bi bi-type-underline"></i></button>
                        <span class="toolbar-separator"></span>
                        <button type="button" class="toolbar-btn" data-action="heading" title="Título"><i class="bi bi-type-h2"></i></button>
                        <button type="button" class="toolbar-btn" data-action="quote" title="Citação"><i class="bi bi-blockquote-left"></i></button>
                        <button type="button" class="toolbar-btn" data-action="list" title="Lista"><i class="bi bi-list-ul"></i></button>
                        <span class="toolbar-separator"></span>
                        <button type="button" class="toolbar-btn" data-action="link" title="Link"><i class="bi bi-link-45deg"></i></button>
                    </div>
                    <textarea name="content" class="form-control @error('content') is-invalid @enderror" id="contentArea"
                              rows="14" required>{{ old('content', $article->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top">
                    <a href="{{ route('articles.show', $article) }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                    <div class="d-flex gap-2">
                        <form action="{{ route('articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Tem a certeza que pretende eliminar este artigo? Esta ação é irreversível.');">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger px-4"><i class="bi bi-trash"></i> Eliminar</button>
                        </form>
                        <button type="submit" class="btn btn-orange btn-lg px-5">
                            <i class="bi bi-check2-circle"></i> Guardar alterações
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.editor-toolbar {
    display: flex;
    align-items: center;
    gap: 2px;
    padding: .5rem .7rem;
    background: #f8f9fc;
    border: 1px solid #dee2e6;
    border-bottom: none;
    border-radius: var(--radius) var(--radius) 0 0;
}
.toolbar-btn {
    border: none;
    background: transparent;
    width: 34px;
    height: 34px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--navy);
    font-size: 1rem;
    cursor: pointer;
    transition: all .15s;
}
.toolbar-btn:hover { background: rgba(255,92,40,.12); color: var(--orange); }
.toolbar-separator { width: 1px; height: 20px; background: #dee2e6; margin: 0 4px; }
#contentArea {
    border-radius: 0 0 var(--radius) var(--radius);
    min-height: 320px;
    font-size: 1.05rem;
    line-height: 1.75;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const contentArea = document.getElementById('contentArea');

    document.querySelectorAll('.toolbar-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const action = btn.dataset.action;
            const start = contentArea.selectionStart;
            const end = contentArea.selectionEnd;
            const selected = contentArea.value.substring(start, end);
            let replacement = '';

            switch (action) {
                case 'bold': replacement = `**${selected || 'texto'}**`; break;
                case 'italic': replacement = `*${selected || 'texto'}*`; break;
                case 'underline': replacement = `<u>${selected || 'texto'}</u>`; break;
                case 'heading': replacement = `\n## ${selected || 'Título'}\n`; break;
                case 'quote': replacement = `\n> ${selected || 'Citação'}\n`; break;
                case 'list': replacement = `\n- ${selected || 'Item da lista'}\n`; break;
                case 'link':
                    const url = prompt('Insira o URL:');
                    if (url) replacement = `[${selected || 'texto do link'}](${url})`;
                    else return;
                    break;
            }

            contentArea.value = contentArea.value.substring(0, start) + replacement + contentArea.value.substring(end);
            contentArea.focus();
        });
    });
});
</script>
@endpush
