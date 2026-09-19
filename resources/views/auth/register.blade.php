@extends('layouts.app')
@section('title', 'Criar conta — '.config('app.name'))

@section('content')
<div class="container auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h3 class="brand"><i class="bi bi-chat-quote-fill"></i> You<span>Say</span></h3>
            <p class="text-muted">Crie a sua conta gratuita</p>
        </div>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Nome completo</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">E-mail</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Palavra-passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Confirmar palavra-passe</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-orange w-100">Criar conta</button>
        </form>
        <p class="text-center text-muted mt-4 mb-0">Já tem conta? <a href="{{ route('login') }}">Entrar</a></p>
    </div>
</div>
@endsection
