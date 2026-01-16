<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    public static $searchable = ['name'];
    public function graduationsProjects(){
        return $this->hasMany(GraduationProjectRequest::class, 'university_id');
    }
    public function participationRequests(){
        return $this->hasMany(InterviewRequest::class, 'university_id');
    }
    public function studentProjects(){
        return $this->hasMany(StudentProject::class, 'university_id');
    }
    public function students(){
        return $this->hasMany(User::class, 'university_id');
    }
}
