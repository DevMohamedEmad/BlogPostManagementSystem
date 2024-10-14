<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'post_id','created_by'];
    
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
