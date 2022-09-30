<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ordinal',
        'image',
        'is_subscriber',
        'category_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function userCourses()
    {
        return $this->hasMany(UserCourse::class);
    }

    public function molecules()
    {
        return $this->hasMany(Molecule::class);
    }
}
