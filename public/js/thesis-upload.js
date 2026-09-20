// Formulário de artigo: alterna entre "artigo normal" e "monografia/dissertação"
// consoante a categoria escolhida, e trata o upload da monografia (PDF), a
// geração automática da capa a partir da 1ª página (via PDF.js, no navegador)
// e o envio direto do PDF para o bucket quando um está configurado.
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('articleForm');
  const categorySelect = form?.querySelector('select[name="category_id"]');
  const normalImageBlock = document.getElementById('normalImageBlock');
  const thesisBlock = document.getElementById('thesisBlock');
  if (!form || !categorySelect || !thesisBlock) return;

  const documentDisk = window.DOCUMENT_DISK || 'public';
  const presignUrl = window.DOCUMENT_PRESIGN_URL;
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

  const documentInput = document.getElementById('documentInput');
  const documentArea = document.getElementById('documentUploadArea');
  const documentPlaceholder = document.getElementById('documentPlaceholder');
  const documentStatus = document.getElementById('documentStatus');
  const coverPreview = document.getElementById('coverPreview');
  const generatedCoverInput = document.getElementById('generatedCoverInput');
  const documentKeyInput = document.getElementById('documentKeyInput');
  const documentOriginalNameInput = document.getElementById('documentOriginalNameInput');
  const submitBtn = document.getElementById('submitBtn');
  const thesisFields = thesisBlock.querySelectorAll('[data-thesis-required]');

  let uploadPending = false;

  function isThesisCategory() {
    const opt = categorySelect.selectedOptions[0];
    return !!opt && opt.dataset.type === 'thesis';
  }

  function syncBlocks() {
    const thesis = isThesisCategory();
    thesisBlock.classList.toggle('d-none', !thesis);
    if (normalImageBlock) normalImageBlock.classList.toggle('d-none', thesis);

    thesisFields.forEach(el => { el.required = thesis; });
    if (documentInput) documentInput.required = thesis && documentDisk !== 's3' && !documentKeyInput.value;
    if (generatedCoverInput) generatedCoverInput.required = thesis && !generatedCoverInput.value;
  }

  categorySelect.addEventListener('change', syncBlocks);
  syncBlocks();

  if (!documentInput) return; // página sem categoria "thesis" disponível

  documentArea.addEventListener('click', () => documentInput.click());
  documentArea.addEventListener('dragover', (e) => { e.preventDefault(); documentArea.classList.add('drag-over'); });
  documentArea.addEventListener('dragleave', () => documentArea.classList.remove('drag-over'));
  documentArea.addEventListener('drop', (e) => {
    e.preventDefault();
    documentArea.classList.remove('drag-over');
    if (e.dataTransfer.files.length) {
      documentInput.files = e.dataTransfer.files;
      handleFile(e.dataTransfer.files[0]);
    }
  });
  documentInput.addEventListener('change', function () {
    if (this.files[0]) handleFile(this.files[0]);
  });

  function setStatus(text, isError) {
    if (!documentStatus) return;
    documentStatus.textContent = text || '';
    documentStatus.classList.toggle('text-danger', !!isError);
    documentStatus.classList.toggle('text-muted', !isError);
  }

  async function handleFile(file) {
    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
      setStatus('Selecione um ficheiro PDF.', true);
      return;
    }

    documentKeyInput.value = '';
    documentOriginalNameInput.value = file.name;
    documentPlaceholder?.classList.add('d-none');
    setStatus('A processar PDF...');

    try {
      await renderCover(file);
    } catch (e) {
      setStatus('Não foi possível gerar a capa automaticamente, mas pode continuar.', true);
    }

    if (documentDisk === 's3') {
      await uploadDirectToBucket(file);
    } else {
      setStatus('PDF pronto: "' + file.name + '"');
    }
  }

  async function renderCover(file) {
    const buffer = await file.arrayBuffer();
    const pdf = await pdfjsLib.getDocument({ data: buffer }).promise;
    const page = await pdf.getPage(1);

    const targetWidth = 600;
    const baseViewport = page.getViewport({ scale: 1 });
    const scale = targetWidth / baseViewport.width;
    const viewport = page.getViewport({ scale });

    const canvas = document.createElement('canvas');
    canvas.width = viewport.width;
    canvas.height = viewport.height;
    await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;

    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
    generatedCoverInput.value = dataUrl;
    if (coverPreview) {
      coverPreview.src = dataUrl;
      coverPreview.classList.remove('d-none');
    }
  }

  async function uploadDirectToBucket(file) {
    uploadPending = true;
    if (submitBtn) submitBtn.disabled = true;
    setStatus('A enviar PDF...');

    try {
      const presignRes = await fetch(presignUrl, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ filename: file.name, size: file.size }),
      });

      if (!presignRes.ok) throw new Error('presign failed');
      const { key, url, headers } = await presignRes.json();

      const putRes = await fetch(url, { method: 'PUT', headers: headers || { 'Content-Type': 'application/pdf' }, body: file });
      if (!putRes.ok) throw new Error('upload failed');

      documentKeyInput.value = key;
      setStatus('PDF enviado: "' + file.name + '"');
    } catch (e) {
      setStatus('Falha ao enviar o PDF. Tente novamente.', true);
      documentKeyInput.value = '';
    } finally {
      uploadPending = false;
      if (submitBtn) submitBtn.disabled = false;
      syncBlocks();
    }
  }

  form.addEventListener('submit', function (e) {
    if (isThesisCategory() && uploadPending) {
      e.preventDefault();
      setStatus('Aguarde o envio do PDF terminar...', true);
    }
  });
});
