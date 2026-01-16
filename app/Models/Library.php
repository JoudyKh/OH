<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Library extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'phone_number',
        'address',
        'section_id',
        'is_special',
        'sort_order',
    ];
    public static $searchable = [
        'name',
        'description',
        'phone_number',
        'address',
        'section_id',
    ];
    public function librarySec(){
        return $this->belongsTo(Section::class, 'section_id');
    }
    public function adViews()
    {
        return $this->morphMany(AdView::class, 'model'); // Links to AdView
    }
    public function images(){
        return $this->hasMany(LibraryImage::class, 'library_id');
    }
}
