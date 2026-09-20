<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function category(Category $category)
    {
        $articles = Article::with(['user', 'category'])
            ->where('category_id', $category->id)
            ->where('is_hidden', false)
            ->latest('published_at')
            ->paginate(9);

        return view('articles.index', ['articles' => $articles, 'titulo' => 'Categoria: '.$category->name]);
    }

    public function my()
    {
        $articles = Auth::user()->articles()->with('category')->latest()->paginate(9);

        return view('articles.my', compact('articles'));
    }

    public function show(Article $article)
    {
        abort_if($article->is_hidden && (! Auth::check() || (! Auth::user()->isAdmin() && Auth::id() !== $article->user_id)), 404);

        $article->increment('views');
        $article->load(['user', 'category', 'comments.user']);

        $userReaction = Auth::check() ? $article->reactions()->where('user_id', Auth::id())->value('type') : null;
        $userRating = Auth::check() ? $article->ratings()->where('user_id', Auth::id())->value('stars') : null;

        $relacionados = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->where('is_hidden', false)
            ->take(3)->get();

        return view('articles.show', compact('article', 'userReaction', 'userRating', 'relacionados'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $documentDisk = Article::documentDisk();

        return view('articles.create', compact('categories', 'documentDisk'));
    }

    public function store(Request $request)
    {
        $category = Category::findOrFail($request->input('category_id'));
        $isThesis = $category->isThesis();

        $validated = $request->validate($this->articleRules($isThesis));

        $data = $this->coreFields($validated);
        $data['user_id'] = Auth::id();

        if ($isThesis) {
            $data['image'] = $this->storeCover($validated['generated_cover']);
            [$data['document_path'], $data['document_original_name']] = $this->storeDocument($request, $validated);
        } elseif ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article = Article::create($data);

        return redirect()->route('articles.show', $article)->with('status', 'Artigo publicado com sucesso!');
    }

    public function edit(Article $article)
    {
        $this->authorizeOwner($article);
        $categories = Category::orderBy('name')->get();
        $documentDisk = Article::documentDisk();

        return view('articles.edit', compact('article', 'categories', 'documentDisk'));
    }

    public function update(Request $request, Article $article)
    {
        $this->authorizeOwner($article);

        $category = Category::findOrFail($request->input('category_id'));
        $isThesis = $category->isThesis();

        $validated = $request->validate($this->articleRules($isThesis, isUpdate: true));

        $data = $this->coreFields($validated);

        if ($isThesis) {
            if (! empty($validated['generated_cover'])) {
                if ($article->image) {
                    Storage::disk('public')->delete($article->image);
                }
                $data['image'] = $this->storeCover($validated['generated_cover']);
            }

            $hasNewDocument = Article::documentDisk() === 's3'
                ? ! empty($validated['document_key'])
                : $request->hasFile('document');

            if ($hasNewDocument) {
                if ($article->document_path) {
                    Storage::disk(Article::documentDisk())->delete($article->document_path);
                }
                [$data['document_path'], $data['document_original_name']] = $this->storeDocument($request, $validated);
            }
        } elseif ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($data);

        return redirect()->route('articles.show', $article)->with('status', 'Artigo atualizado com sucesso!');
    }

    public function destroy(Article $article)
    {
        $this->authorizeOwner($article);

        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }
        if ($article->document_path) {
            Storage::disk(Article::documentDisk())->delete($article->document_path);
        }
        $article->delete();

        return redirect()->route('articles.my')->with('status', 'Artigo eliminado.');
    }

    private function authorizeOwner(Article $article): void
    {
        abort_unless(Auth::id() === $article->user_id || Auth::user()?->isAdmin(), 403, 'Não tem permissão para editar este artigo.');
    }

    /**
     * Regras de validação do formulário de artigo. Quando a categoria é do
     * tipo "thesis" (Monografias e Dissertações), troca a imagem manual por
     * um PDF + capa gerada no navegador e pede os dados académicos.
     */
    private function articleRules(bool $isThesis, bool $isUpdate = false): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
        ];

        if (! $isThesis) {
            $rules['image'] = 'nullable|image|max:4096';

            return $rules;
        }

        $required = $isUpdate ? 'nullable' : 'required';

        $rules += [
            'author_name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'academic_level' => 'nullable|string|in:Licenciatura,Mestrado,Doutoramento',
            'completion_year' => ['nullable', 'integer', 'min:1980', 'max:'.(now()->year + 1)],
            'country' => 'nullable|string|max:255',
            'generated_cover' => $required.'|string',
        ];

        if (Article::documentDisk() === 's3') {
            $rules['document_key'] = $required.'|string|max:255';
            $rules['document_original_name'] = 'nullable|string|max:255';
        } else {
            $rules['document'] = $required.'|file|mimes:pdf|max:25600';
        }

        return $rules;
    }

    private function coreFields(array $validated): array
    {
        return array_intersect_key($validated, array_flip([
            'title', 'category_id', 'content', 'published_at',
            'author_name', 'institution', 'course', 'academic_level', 'completion_year', 'country',
        ]));
    }

    /**
     * Descodifica a capa gerada no navegador (data URL base64, 1ª página do
     * PDF desenhada num canvas) e grava-a no disco 'public', tal como a
     * imagem de capa manual dos artigos normais.
     */
    private function storeCover(string $dataUrl): ?string
    {
        if (! preg_match('/^data:image\/(png|jpe?g);base64,(.+)$/', $dataUrl, $matches)) {
            return null;
        }

        $binary = base64_decode($matches[2], true);
        if ($binary === false || strlen($binary) > 5 * 1024 * 1024) {
            return null;
        }

        $extension = $matches[1] === 'png' ? 'png' : 'jpg';
        $path = 'articles/'.Str::uuid().'.'.$extension;
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    /**
     * Com bucket S3 configurado, o PDF já foi enviado diretamente pelo
     * navegador via URL pré-assinada (ver ArticleDocumentController::presign);
     * aqui só confirmamos a chave. Sem bucket, o PDF vem no próprio POST.
     */
    private function storeDocument(Request $request, array $validated): array
    {
        if (Article::documentDisk() === 's3') {
            return [$validated['document_key'], $validated['document_original_name'] ?? 'monografia.pdf'];
        }

        $file = $request->file('document');

        return [$file->store('monografias', 'public'), $file->getClientOriginalName()];
    }
}
