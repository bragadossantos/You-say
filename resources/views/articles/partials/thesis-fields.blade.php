@php($thesisArticle = $article ?? null)
{{-- Monografias e Dissertações: substitui a imagem manual por um PDF (a capa é
     gerada automaticamente a partir da 1ª página, no navegador) e pede os
     dados académicos. Mostrado apenas quando a categoria escolhida é do tipo
     "thesis" — ver public/js/thesis-upload.js. --}}
<div id="thesisBlock" class="d-none">

    <div class="row">
        <div class="col-md-6 mb-4">
            <label class="form-label fw-semibold"><i class="bi bi-person-badge"></i> Nome do autor</label>
            <input type="text" name="author_name" class="form-control @error('author_name') is-invalid @enderror"
                   value="{{ old('author_name', $thesisArticle?->author_name) }}" maxlength="255" data-thesis-required
                   placeholder="Nome completo de quem escreveu o trabalho">
            @error('author_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-4">
            <label class="form-label fw-semibold"><i class="bi bi-bank"></i> Universidade / Instituição</label>
            <input type="text" name="institution" class="form-control @error('institution') is-invalid @enderror"
                   value="{{ old('institution', $thesisArticle?->institution) }}" maxlength="255" data-thesis-required
                   placeholder="Ex: Universidade Gregório Semedo">
            @error('institution') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <label class="form-label fw-semibold"><i class="bi bi-mortarboard"></i> Curso / Área</label>
            <input type="text" name="course" class="form-control @error('course') is-invalid @enderror"
                   value="{{ old('course', $thesisArticle?->course) }}" maxlength="255"
                   placeholder="Ex: Engenharia Informática">
            @error('course') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-4">
            <label class="form-label fw-semibold"><i class="bi bi-award"></i> Nível académico</label>
            <select name="academic_level" class="form-select @error('academic_level') is-invalid @enderror">
                <option value="">Selecione...</option>
                @foreach (['Licenciatura', 'Mestrado', 'Doutoramento'] as $nivel)
                    <option value="{{ $nivel }}" {{ old('academic_level', $thesisArticle?->academic_level) === $nivel ? 'selected' : '' }}>{{ $nivel }}</option>
                @endforeach
            </select>
            @error('academic_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-4">
            <label class="form-label fw-semibold"><i class="bi bi-calendar-check"></i> Ano de conclusão</label>
            <input type="number" name="completion_year" class="form-control @error('completion_year') is-invalid @enderror"
                   value="{{ old('completion_year', $thesisArticle?->completion_year) }}" min="1980" max="{{ now()->year + 1 }}">
            @error('completion_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold"><i class="bi bi-flag"></i> País de origem</label>
        @php($paises = ['Angola', 'Moçambique', 'Cabo Verde', 'Guiné-Bissau', 'São Tomé e Príncipe', 'Portugal', 'Brasil', 'Timor-Leste'])
        <select name="country" class="form-select @error('country') is-invalid @enderror">
            @foreach ($paises as $pais)
                <option value="{{ $pais }}" {{ old('country', $thesisArticle?->country ?? 'Angola') === $pais ? 'selected' : '' }}>{{ $pais }}</option>
            @endforeach
        </select>
        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold"><i class="bi bi-file-earmark-pdf"></i> Ficheiro da monografia (PDF)</label>
        <div class="upload-area" id="documentUploadArea">
            {{-- name="document" só quando NÃO há bucket S3: nesse modo o PDF é
                 enviado diretamente pelo navegador para o bucket (ver
                 thesis-upload.js), e não deve seguir também no POST do form. --}}
            <input type="file" id="documentInput" accept=".pdf,application/pdf" class="d-none"
                   @if (($documentDisk ?? 'public') !== 's3') name="document" @endif>
            <div id="documentPlaceholder" class="text-center py-4">
                <i class="bi bi-file-earmark-arrow-up" style="font-size: 2.5rem; color: var(--orange);"></i>
                <p class="mb-1 mt-2 fw-semibold">Clique ou arraste o PDF da monografia</p>
                <p class="text-muted small mb-0">Apenas PDF • Máximo 25MB — a capa é gerada automaticamente</p>
            </div>
        </div>
        <p class="small mt-2 mb-0" id="documentStatus">
            @if ($thesisArticle?->document_path)
                PDF atual: {{ $thesisArticle->document_original_name ?? 'documento.pdf' }} (envie um novo apenas para substituir)
            @endif
        </p>
        @error('document') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        @error('document_key') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

        <img id="coverPreview" class="rounded-3 mt-3 {{ $thesisArticle?->image ? '' : 'd-none' }}"
             style="max-height: 220px;" src="{{ $thesisArticle?->image ? $thesisArticle->imageUrl() : '' }}" alt="Capa gerada">
    </div>

    <input type="hidden" name="generated_cover" id="generatedCoverInput" value="">
    <input type="hidden" name="document_key" id="documentKeyInput" value="">
    <input type="hidden" name="document_original_name" id="documentOriginalNameInput" value="{{ $thesisArticle?->document_original_name }}">
</div>
