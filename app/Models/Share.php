<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    protected $fillable = ['article_id', 'user_id', 'platform'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
