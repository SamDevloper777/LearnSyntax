<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'chapter_name',
        'chapter_description',
        'chapter_slug',
        'order'
       
    ];

    public function course(){
        return $this->hasMany(Courses::class,"id","course_id");
    }

}
