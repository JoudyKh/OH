<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lecture extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'type',
        'description',
        'requirements',
        'requirements_image',
        'city',
        'classification_id',
        'notes',
        'sub_section_id',
        'sort_order',
    ];
    public static $searchable = [
        'name',
        'type',
        'city',
        'classification_id',
        'sub_section_id',
    ];

    public function subSection()
    {
        return $this->belongsTo(Section::class, 'sub_section_id');
    }
    public function images()
    {
        return $this->hasMany(LectureImage::class, 'lecture_id');
    }
    public function attachedFiles()
    {
        return $this->hasMany(LectureAttachedFile::class, 'lecture_id');
    }
    public function paragraphs()
    {
        return $this->hasMany(LectureParagraph::class, 'lecture_id');
    }
    public function links()
    {
        return $this->hasMany(LectureLink::class, 'lecture_id');
    }
    public function adViews()
    {
        return $this->morphMany(AdView::class, 'model'); // Links to AdView
    }
    public function classification(){
        return $this->belongsTo(Classification::class, 'classification_id');
    }
}
