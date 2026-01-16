<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProjectFile extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_project_id',
        'file',
        'name',
    ];
    public function studentProject(){
        return $this->belongsTo(StudentProject::class, 'student_project_id');
    }
}
