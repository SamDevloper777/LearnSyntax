<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }


    protected $fillable = [
        'topic_id',
        'title',
        'description',
        'author_id',
        'status',
    ];
}
