<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'library_id',
        'image',
    ];
    public function library(){
        return $this->belongsTo(Library::class, 'library_id');
    }
}
