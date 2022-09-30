<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Molecule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ordinal',
        'course_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function atoms()
    {
        return $this->hasMany(Atom::class);
    }
}
