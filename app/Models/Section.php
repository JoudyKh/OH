<?php

namespace App\Models;

use App\Constants\Constants;
use App\Observers\SectionObserver;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected function asJson($value): bool|string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    protected $fillable = [
        'parent_id',
        'type',
        'name',
        'image',
        'sub_type',
        'description',
        'is_special',
        'sort_order',
    ];
    public static $searchable = [
        'parent_id',
        'type',
        'name',
        'sub_type',
    ];
    protected $hidden = [];

    protected static function boot()
    {
        parent::boot();
        self::observe(SectionObserver::class);
        static::retrieved(function ($model) {
            $model->hidden = $model->getHiddenAttributes();
        });

        static::saving(function ($model) {
            $model->hidden = $model->getHiddenAttributes();
        });
    }



    public function getHiddenAttributes(): array
    {
        $sectionAttributes = Constants::SECTIONS_TYPES[$this->type]['attributes'];
        $sectionAttributes[] = 'id';
        $sectionAttributes[] = 'type';
        $sectionAttributes[] = 'sub_type';
        $allAttributes = Schema::getColumnListing($this->getTable());

        return array_diff($allAttributes, $sectionAttributes);
    }


    public function subSections(): HasMany
    {
        return $this->hasMany(Section::class, 'parent_id');
    }
    public function superSection(): BelongsTo{
        return $this->belongsTo(Section::class, 'parent_id');
    }
    // for library section
    public function libraries(){
        return $this->hasMany(Library::class, 'section_id');
    }
    // for sub section lectures
    public function lectures(){
        return $this->hasMany(Lecture::class, 'sub_section_id');
    }
    // for site libraries sections
    public function subLibrariesSec(){
        return $this->hasMany(Section::class, 'parent_id');
    }
    // for sections of the site library
    public function superLibrarySec(){
        return $this->belongsTo(Section::class, 'parent_id');
    }
    public function adViews()
    {
        return $this->morphMany(AdView::class, 'model'); // Links to AdView
    }
    // for sub library section
    public function LibraryFiles(){
        return $this->hasMany(LibraryFile::class, 'sub_library_id');
    }

}
