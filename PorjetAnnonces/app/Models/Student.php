<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'school',
        'study_level',
        'field_of_study',
        'linkedin_url',
        'facebook_url',
        'cv_path',
        'profile_photo',
        'bio'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'user_id', 'user_id');
    }
}
