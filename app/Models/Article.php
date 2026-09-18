<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'content', 'image', 'published_at', 'views', 'is_hidden',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'is_hidden' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Article $article) {
            if (empty($article->slug)) {
                $base = Str::slug($article->title);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $article->slug = $slug;
            }
            if (empty($article->published_at)) {
                $article->published_at = now();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->where('is_hidden', false)->latest();
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function shares()
    {
        return $this->hasMany(Share::class);
    }

    public function likesCount(): int
    {
        return $this->reactions()->where('type', 'like')->count();
    }

    public function dislikesCount(): int
    {
        return $this->reactions()->where('type', 'dislike')->count();
    }

    public function averageRating(): float
    {
        return round($this->ratings()->avg('stars') ?? 0, 1);
    }

    public function imageUrl(): string
    {
        return $this->image
            ? asset('storage/'.$this->image)
            : 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=900&q=80';
    }

    public function excerpt(int $limit = 140): string
    {
        return Str::limit(strip_tags($this->content), $limit);
    }
}
