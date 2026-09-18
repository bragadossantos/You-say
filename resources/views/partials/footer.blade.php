<footer class="site-footer py-5 mt-5">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <h5 class="text-white"><i class="bi bi-rocket-takeoff"></i> Artigos<span style="color:#FF5C28">UGS</span></h5>
                <p class="small mb-0">Plataforma aberta de publicação de artigos da comunidade UGS/FENT. Escreva, partilhe e descubra novos conhecimentos.</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-white">Categorias</h6>
                <ul class="list-unstyled small">
                    @foreach (\App\Models\Category::orderBy('name')->take(5)->get() as $cat)
                        <li><a href="{{ route('articles.category', $cat) }}">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-white">Conta</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('login') }}">Entrar</a></li>
                    <li><a href="{{ route('register') }}">Criar conta</a></li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <p class="small text-center mb-0">&copy; {{ date('Y') }} Artigos UGS — Faculdade de Engenharia e Novas Tecnologias (FENT). Todos os direitos reservados.</p>
    </div>
</footer>
