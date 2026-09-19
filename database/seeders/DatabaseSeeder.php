<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@artigos.local',
            'password' => 'password',
            'role' => 'admin',
            'bio' => 'Administrador da plataforma YouSay.',
        ]);

        $autor = User::create([
            'name' => 'Braga',
            'email' => 'braga@artigos.local',
            'password' => 'password',
            'role' => 'user',
            'bio' => 'Estudante de Engenharia Informática na UGS/FENT.',
        ]);

        $categorias = [
            ['name' => 'Tecnologia', 'icon' => 'bi-cpu'],
            ['name' => 'Ciência', 'icon' => 'bi-stars'],
            ['name' => 'Educação', 'icon' => 'bi-mortarboard'],
            ['name' => 'Sociedade', 'icon' => 'bi-people'],
            ['name' => 'Desporto', 'icon' => 'bi-trophy'],
            ['name' => 'Cultura', 'icon' => 'bi-palette'],
        ];

        foreach ($categorias as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'icon' => $cat['icon'],
            ]);
        }

        $exemplos = [
            ['title' => 'O futuro da Inteligência Artificial em Angola', 'cat' => 'Tecnologia'],
            ['title' => 'Como a exploração espacial inspira novas gerações', 'cat' => 'Ciência'],
            ['title' => 'A importância do ensino superior tecnológico', 'cat' => 'Educação'],
        ];

        foreach ($exemplos as $ex) {
            Article::create([
                'user_id' => $autor->id,
                'category_id' => Category::where('name', $ex['cat'])->first()->id,
                'title' => $ex['title'],
                'content' => "<p>Este é um artigo de exemplo gerado automaticamente para demonstrar a plataforma. Edite ou remova este conteúdo e comece a publicar os seus próprios artigos.</p><p>A plataforma YouSay foi criada para permitir que qualquer utilizador partilhe conhecimento de forma livre e organizada, com categorias, comentários, avaliações e muito mais.</p>",
                'published_at' => now(),
            ]);
        }
    }
}
