<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleDocumentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\ShareController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/pesquisar', [HomeController::class, 'search'])->name('search');
Route::get('/categoria/{category:slug}', [ArticleController::class, 'category'])->name('articles.category');

require __DIR__.'/auth.php';

// Article CRUD (auth required) — must come BEFORE the wildcard show route
Route::middleware('auth')->group(function () {
    Route::get('/meus-artigos', [ArticleController::class, 'my'])->name('articles.my');
    Route::get('/artigos/criar', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/artigos', [ArticleController::class, 'store'])->name('articles.store');
    Route::post('/artigos/monografia/presign', [ArticleDocumentController::class, 'presign'])->name('articles.document.presign');
    Route::get('/artigos/{article:slug}/editar', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/artigos/{article:slug}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/artigos/{article:slug}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    Route::post('/artigos/{article:slug}/comentarios', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/artigos/{article:slug}/comentarios/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::post('/artigos/{article:slug}/reacao', [ReactionController::class, 'store'])->name('reactions.store');
    Route::delete('/artigos/{article:slug}/reacao', [ReactionController::class, 'destroy'])->name('reactions.destroy');

    Route::post('/artigos/{article:slug}/avaliar', [RatingController::class, 'store'])->name('ratings.store');
    Route::post('/artigos/{article:slug}/partilhar', [ShareController::class, 'store'])->name('shares.store');

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
});

// Public article show — AFTER specific routes so /artigos/criar is not caught
Route::get('/artigos/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/artigos/{article:slug}/documento', [ArticleDocumentController::class, 'view'])->name('articles.document.view');
Route::get('/artigos/{article:slug}/documento/download', [ArticleDocumentController::class, 'download'])->name('articles.document.download');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/utilizadores', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/utilizadores/{user}/bloquear', [AdminUserController::class, 'toggleBlock'])->name('users.toggleBlock');
    Route::patch('/utilizadores/{user}/admin', [AdminUserController::class, 'toggleAdmin'])->name('users.toggleAdmin');
    Route::delete('/utilizadores/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/artigos', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::patch('/artigos/{article}/ocultar', [AdminArticleController::class, 'toggleHidden'])->name('articles.toggleHidden');
    Route::delete('/artigos/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');

    Route::get('/categorias', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categorias', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categorias/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categorias/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/comentarios', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::patch('/comentarios/{comment}/ocultar', [AdminCommentController::class, 'toggleHidden'])->name('comments.toggleHidden');
    Route::delete('/comentarios/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');
});
