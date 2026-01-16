<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProject extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'full_name',
        'year',
        'subject',
        'phone_number',
        'status',
        'mark',
        'student_id',
        'university_id',
        'note',
    ];
    public static $searchable = [
        'subject',
        'status',
        'university_id',
        'full_name',
        'year',
    ];
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i A'); 
    }
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i A'); 
    }
    public function files(){
        return $this->hasMany(StudentProjectFile::class, 'student_project_id');
    }
    public function student(){
        return $this->belongsTo(User::class, 'student_id');
    }
    public function university(){
        return $this->belongsTo(University::class, 'university_id');
    }
}
