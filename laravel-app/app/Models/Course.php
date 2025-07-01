<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'site',
        'description',
        'price',
        'image',
        'teacher_id',
    ];

    public function teacher()
    {
        
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function members()
    {
        return $this->hasMany(CourseMember::class, 'course_id');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function contents()
    {
        return $this->hasMany(CourseContent::class, 'course_id');
    }
    
}
