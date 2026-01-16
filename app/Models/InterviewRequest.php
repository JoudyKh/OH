<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'type',
        'name',
        'academic_achievement',
        'university_id',
        'phone_number',
        'first_name',
        'father_name',
        'last_name',
        'mother_name',
        'birth_place',
        'birth_date',
        'national_id',
        'registration_place',
        'central_secretariat',
        'gender',
        'email',
        'address',
        'delivery_address',
        // 'student_id',
        'interview_id',
    ];
    public static $searchable = [
        'type',
    ];
    public function interview(){
        return $this->belongsTo(Interview::class, 'interview_id');
    }
    // public function student(){
    //     return $this->belongsTo(User::class, 'student_id');
    // }
    public function university(){
        return $this->belongsTo(University::class, 'university_id');
    }
}
