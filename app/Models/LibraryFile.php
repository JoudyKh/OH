<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryFile extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'file',
        'sub_library_id',
        'sort_order',
    ];
    public static $searchable = [
        'name',
        'created_at',
        'sub_library_id',
    ];
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i'); 
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i'); 
    }
    public function subLibrary(){
        return $this->belongsTo(Section::class, 'sub_library_id');
    }
}
