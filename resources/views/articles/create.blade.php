@extends('layouts.app')
@section('title', 'Publicar artigo — '.config('app.name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="section-title mt-0"><div class="bar"></div><h3>Publicar novo artigo</h3></div>

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

            <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 p-md-5 rounded-4 shadow-sm" id="articleForm">
                @csrf

                {{-- Title --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold"><i class="bi bi-type-h1"></i> Título do artigo</label>
                    <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" required maxlength="255"
                           placeholder="Escreva um título atrativo para o seu artigo...">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-end"><span id="titleCount">0</span>/255</div>
                </div>

                {{-- Category + Date --}}
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold"><i class="bi bi-tag"></i> Categoria</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Selecione uma categoria...</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                               value="{{ old('published_at', date('Y-m-d')) }}">
                        @error('published_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Image Upload --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold"><i class="bi bi-image"></i> Imagem de capa</label>
                    <div class="upload-area" id="uploadArea">
                        <input type="file" name="image" class="form-control d-none @error('image') is-invalid @enderror"
                               id="imageInput" accept="image/*">
                        <div id="uploadPlaceholder" class="text-center py-4">
                            <i class="bi bi-cloud-arrow-up" style="font-size: 2.5rem; color: var(--orange);"></i>
                            <p class="mb-1 mt-2 fw-semibold">Clique ou arraste uma imagem</p>
                            <p class="text-muted small mb-0">JPG, PNG ou WebP • Máximo 4MB</p>
                        </div>
                        <div id="imagePreviewContainer" class="d-none position-relative">
                            <img id="imagePreview" class="w-100 rounded-3" style="max-height: 300px; object-fit: cover;">
                            <button type="button" id="removeImage" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
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
                              rows="14" required placeholder="Escreva o conteúdo do seu artigo aqui...">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-end"><span id="wordCount">0</span> palavras</div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top">
                    <a href="{{ route('articles.my') }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-orange btn-lg px-5">
                        <i class="bi bi-send-fill"></i> Publicar artigo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.upload-area {
    border: 2px dashed #d0d5dd;
    border-radius: var(--radius);
    cursor: pointer;
    transition: border-color .2s, background .2s;
    overflow: hidden;
}
.upload-area:hover, .upload-area.drag-over {
    border-color: var(--orange);
    background: rgba(255, 92, 40, .04);
}
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
    // Title character counter
    const titleInput = document.querySelector('input[name="title"]');
    const titleCount = document.getElementById('titleCount');
    titleInput.addEventListener('input', () => titleCount.textContent = titleInput.value.length);
    titleCount.textContent = titleInput.value.length;

    // Word counter
    const contentArea = document.getElementById('contentArea');
    const wordCount = document.getElementById('wordCount');
    function updateWordCount() {
        const text = contentArea.value.trim();
        wordCount.textContent = text ? text.split(/\s+/).length : 0;
    }
    contentArea.addEventListener('input', updateWordCount);
    updateWordCount();

    // Image upload & preview
    const uploadArea = document.getElementById('uploadArea');
    const imageInput = document.getElementById('imageInput');
    const placeholder = document.getElementById('uploadPlaceholder');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const preview = document.getElementById('imagePreview');
    const removeBtn = document.getElementById('removeImage');

    uploadArea.addEventListener('click', (e) => {
        if (e.target.closest('#removeImage')) return;
        imageInput.click();
    });

    uploadArea.addEventListener('dragover', (e) => { e.preventDefault(); uploadArea.classList.add('drag-over'); });
    uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        if (e.dataTransfer.files.length) {
            imageInput.files = e.dataTransfer.files;
            showPreview(e.dataTransfer.files[0]);
        }
    });

    imageInput.addEventListener('change', function () {
        if (this.files[0]) showPreview(this.files[0]);
    });

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            placeholder.classList.add('d-none');
            previewContainer.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }

    removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        imageInput.value = '';
        placeholder.classList.remove('d-none');
        previewContainer.classList.add('d-none');
    });

    // Simple toolbar formatting
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
            contentArea.selectionStart = start;
            contentArea.selectionEnd = start + replacement.length;
            updateWordCount();
        });
    });
});
</script>
@endpush
