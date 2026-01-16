<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    public static $searchable = ['name'];
    public function projects(){
        return $this->hasMany(Lecture::class, 'classification_id');
    }
}
