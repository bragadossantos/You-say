<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleDocumentController extends Controller
{
    /**
     * Gera uma URL pré-assinada para o navegador enviar o PDF diretamente para
     * o bucket S3-compatível, contornando o limite de payload da função
     * serverless da Vercel. Só existe quando um bucket está configurado —
     * sem ele, o formulário envia o PDF normalmente no POST (disco 'public').
     */
    public function presign(Request $request)
    {
        abort_unless(Article::documentDisk() === 's3', 404);

        $data = $request->validate([
            'filename' => 'required|string|max:255',
            'size' => 'required|integer|min:1|max:'.(25 * 1024 * 1024),
        ]);

        abort_unless(Str::endsWith(strtolower($data['filename']), '.pdf'), 422, 'Apenas ficheiros PDF são permitidos.');

        $key = 'monografias/'.Str::uuid().'.pdf';

        $signed = Storage::disk('s3')->temporaryUploadUrl(
            $key,
            now()->addMinutes(15),
            ['ContentType' => 'application/pdf']
        );

        return response()->json([
            'key' => $key,
            'url' => $signed['url'] ?? $signed,
            'headers' => $signed['headers'] ?? ['Content-Type' => 'application/pdf'],
        ]);
    }

    public function view(Article $article)
    {
        $this->authorizeAccess($article);

        if (Article::documentDisk() === 'public') {
            $filename = $article->document_original_name ?: ($article->slug.'.pdf');

            return Storage::disk('public')->response($article->document_path, $filename);
        }

        return redirect()->away($article->documentUrl('inline'));
    }

    public function download(Article $article)
    {
        $this->authorizeAccess($article);

        $filename = $article->document_original_name ?: ($article->slug.'.pdf');

        if (Article::documentDisk() === 'public') {
            return Storage::disk('public')->download($article->document_path, $filename);
        }

        return redirect()->away($article->documentUrl('attachment'));
    }

    private function authorizeAccess(Article $article): void
    {
        abort_unless($article->isThesis() && $article->document_path, 404);
        abort_if(
            $article->is_hidden && (! Auth::check() || (! Auth::user()->isAdmin() && Auth::id() !== $article->user_id)),
            404
        );
    }
}
