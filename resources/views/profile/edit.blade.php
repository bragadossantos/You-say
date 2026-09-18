@extends('layouts.app')
@section('title', 'Meu perfil — '.config('app.name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="section-title mt-0"><div class="bar"></div><h3>O meu perfil</h3></div>

            <div class="bg-white p-4 rounded-4 shadow-sm">
                <div class="text-center mb-4">
                    <img src="{{ $user->avatarUrl() }}" class="rounded-circle" width="100" height="100" style="object-fit:cover;">
                </div>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto de perfil</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">E-mail</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Biografia</label>
                        <textarea name="bio" class="form-control" rows="3" maxlength="500">{{ old('bio', $user->bio) }}</textarea>
                    </div>
                    <hr>
                    <p class="text-muted small">Deixe em branco caso não deseje alterar a palavra-passe.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nova palavra-passe</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirmar nova palavra-passe</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-orange px-4">Guardar alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
