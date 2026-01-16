<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureParagraph extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'description',
        'lecture_id',
    ];
    public function lecture(){
        return $this->belongsTo(Lecture::class, 'lecture_id');
    }
}
