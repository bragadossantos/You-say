<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.jpg') }}">
    <title>@yield('title', 'Admin — '.config('app.name'))</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <div class="admin-sidebar" style="width:250px;">
        <div class="p-4">
            <a href="{{ route('home') }}" class="navbar-brand text-white"><i class="bi bi-chat-quote-fill"></i> You<span style="color:#FF5C28">Say</span></a>
            <div class="text-white-50 small mt-1">Painel Administrativo</div>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Utilizadores</a>
        <a href="{{ route('admin.articles.index') }}" class="{{ request()->routeIs('admin.articles.*') ? 'active' : '' }}"><i class="bi bi-journal-text"></i> Artigos</a>
        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="bi bi-tags"></i> Categorias</a>
        <a href="{{ route('admin.comments.index') }}" class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}"><i class="bi bi-chat-dots"></i> Comentários</a>
        <a href="{{ route('home') }}"><i class="bi bi-box-arrow-left"></i> Voltar ao site</a>
        <form action="{{ route('logout') }}" method="POST" class="px-4 mt-2">
            @csrf
            <button class="btn btn-sm btn-outline-light w-100">Sair</button>
        </form>
    </div>

    <div class="flex-grow-1 p-4" style="background:#F4F6FB; min-height:100vh;">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('status') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
