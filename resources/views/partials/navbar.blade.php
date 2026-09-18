<nav class="navbar navbar-expand-lg navbar-artigos sticky-top py-3">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}"><i class="bi bi-rocket-takeoff"></i> Artigos<span>UGS</span></a>
        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Início</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('articles.my') ? 'active' : '' }}" href="{{ route('articles.my') }}">Os meus artigos</a></li>
                @endauth
                @auth
                    @if (Auth::user()->isAdmin())
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Admin</a></li>
                    @endif
                @endauth
            </ul>

            <form action="{{ route('search') }}" method="GET" class="d-flex me-lg-3 mb-2 mb-lg-0" role="search">
                <input class="form-control form-control-sm" type="search" name="q" placeholder="Pesquisar artigos..." value="{{ request('q') }}" style="min-width:220px;">
            </form>

            <div class="d-flex align-items-center gap-2">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-orange">Entrar</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-orange">Criar conta</a>
                @else
                    <a href="{{ route('articles.create') }}" class="btn btn-sm btn-orange"><i class="bi bi-plus-lg"></i> Publicar</a>
                    <div class="dropdown">
                        <a class="d-flex align-items-center gap-2 text-white text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="{{ Auth::user()->avatarUrl() }}" class="rounded-circle" width="32" height="32" style="object-fit:cover;">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">{{ Auth::user()->name }}</h6></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Perfil</a></li>
                            <li><a class="dropdown-item" href="{{ route('articles.my') }}"><i class="bi bi-journal-text"></i> Os meus artigos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Sair</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>
