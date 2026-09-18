@extends('layouts.app')
@section('title', 'Publicar artigo — '.config('app.name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="section-title mt-0"><div class="bar"></div><h3>Publicar novo artigo</h3></div>

            <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded-4 shadow-sm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Título</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required maxlength="255">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Categoria</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Data de publicação</label>
                        <input type="date" name="published_at" class="form-control" value="{{ old('published_at', date('Y-m-d')) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Imagem do artigo</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <div class="form-text">Apenas uma imagem por artigo (máx. 4MB).</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Conteúdo</label>
                    <textarea name="content" class="form-control" rows="10" required>{{ old('content') }}</textarea>
                </div>
                <button type="submit" class="btn btn-orange px-4"><i class="bi bi-send"></i> Publicar artigo</button>
            </form>
        </div>
    </div>
</div>
@endsection
