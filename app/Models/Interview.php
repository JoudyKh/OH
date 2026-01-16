<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'date',
        'place',
        'description',
        'is_special',
        'type',
        'requests_type',
        'sort_order',
    ];
    public static $searchable = [
        'name',
    ];
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i'); 
    }
    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i A'); 
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i'); 
    }
    public function images()
    {
        return $this->hasMany(InterviewImage::class, 'interview_id');
    }
    public function requests()
    {
        return $this->hasMany(InterviewRequest::class, 'interview_id');
    }
}
