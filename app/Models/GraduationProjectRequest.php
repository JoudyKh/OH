<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GraduationProjectRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'phone_number',
        'subject',
        'university_id',
    ];
    public static $searchable = [
        'university_id',
        'name',
        'phone_number',
    ];
    public function university(){
        return $this->belongsTo(University::class, 'university_id');
    }
}
