# Artigos UGS — Plataforma de Publicação de Artigos

Plataforma web aberta para publicação de artigos, construída em **Laravel 11**, com frontend em **Bootstrap 5** + **CSS3** + **JavaScript**, com um design inspirado no site NASA Space Apps (tons de azul-marinho + laranja, tipografia forte, cartões arredondados).

## Funcionalidades

**Utilizador público**
- Registo, login, edição de perfil (nome, e-mail, bio, foto, palavra-passe)
- Criar, editar e eliminar os próprios artigos (imagem, título, conteúdo, categoria, data)
- Like / Dislike em artigos
- Comentar artigos
- Avaliação de 1 a 5 estrelas
- Partilha (WhatsApp, Facebook, X, copiar link)
- Pesquisa de artigos
- Página inicial com carrossel de destaques, artigos recentes, populares e categorias
- Página individual de leitura de artigo, com artigos relacionados

**Administrador** (`/admin`)
- Dashboard com estatísticas (utilizadores, artigos, comentários, likes, dislikes, avaliações, partilhas, visualizações) e atividade recente
- Gestão de utilizadores (bloquear/desbloquear, promover/remover admin, remover conta)
- Gestão de artigos (ocultar/mostrar, remover)
- Gestão de categorias (criar, remover)
- Moderação de comentários (ocultar/mostrar, remover)

## Estrutura técnica

- **Backend:** Laravel 11 (MVC), autenticação própria por sessão, migrations para base de dados relacional (MySQL)
- **Views:** Blade (sem frontend framework complexo)
- **Estilo:** Bootstrap 5 + Bootstrap Icons + CSS3 personalizado (`public/css/style.css`)
- **JS:** vanilla JS (`public/js/app.js`) para like/dislike, avaliação por estrelas e partilha via AJAX
- **Permissões:** coluna `role` em `users` (`user` / `admin`) + middleware `EnsureUserIsAdmin`
- **Uploads:** imagens guardadas em `storage/app/public` (disco `public`, requer `php artisan storage:link`)

## Modelo de dados

- `users` (com `role`, `bio`, `avatar`, `is_blocked`)
- `categories`
- `articles` (uma imagem, título, conteúdo, categoria, data de publicação, vistas, `is_hidden`)
- `comments` (com `is_hidden` para moderação)
- `reactions` (like/dislike, único por utilizador/artigo)
- `ratings` (1 a 5 estrelas, único por utilizador/artigo)
- `shares` (registo de partilhas por plataforma)

## Instalação local

Requisitos: PHP 8.2+, Composer, MySQL (ou compatível).

```bash
# 1. Instalar dependências
composer install

# 2. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 3. Editar o .env com os dados da sua base de dados MySQL
#    DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 4. Criar as tabelas e dados de exemplo
php artisan migrate --seed

# 5. Ligar o storage público (para as imagens dos artigos/avatares)
php artisan storage:link

# 6. Arrancar o servidor de desenvolvimento
php artisan serve
```

Aceda em `http://localhost:8000`.

### Conta de administrador (criada pelo seeder)
- E-mail: `admin@artigos.local`
- Palavra-passe: `password`

### Conta de exemplo (autor)
- E-mail: `braga@artigos.local`
- Palavra-passe: `password`

> **Importante:** altere estas palavras-passe antes de colocar o site em produção.

## Notas

- O projeto está pronto a correr após `composer install` — os pacotes de terceiros (Laravel Framework, Breeze-like auth manual, etc.) são descarregados automaticamente do Packagist.
- Para produção, configure `APP_ENV=production`, `APP_DEBUG=false`, e gere assets/otimize com `php artisan config:cache` e `php artisan route:cache`.
- O carrossel do cabeçalho usa os 5 artigos mais recentes; publique alguns artigos com imagem para o ver completo.
