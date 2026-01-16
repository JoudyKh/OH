<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureAttachedFile extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'file',
        'image',
        'lecture_id',
    ];
    public function lecture(){
        return $this->belongsTo(Lecture::class, 'lecture_id');
    }
}
