@extends('layouts.app')
@section('title', 'Entrar — '.config('app.name'))

@section('content')
<div class="container auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h3 class="brand"><i class="bi bi-chat-quote-fill"></i> You<span>Say</span></h3>
            <p class="text-muted">Entre na sua conta</p>
        </div>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">E-mail</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Palavra-passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Manter sessão iniciada</label>
            </div>
            <button type="submit" class="btn btn-orange w-100">Entrar</button>
        </form>
        <p class="text-center text-muted mt-4 mb-0">Não tem conta? <a href="{{ route('register') }}">Registe-se</a></p>
    </div>
</div>
@endsection
