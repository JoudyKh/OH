<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'interview_id',
        'image',
    ];
    public function interview(){
        return $this->belongsTo(Interview::class, 'interview_id');
    }
}
