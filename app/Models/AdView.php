<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdView extends Model
{
    use HasFactory;
    protected $fillable = [
        'ip',
        'country',
        'finger_print',
        'geo_info',
        'model_type',  
        'model_id',    
        'view',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
}
