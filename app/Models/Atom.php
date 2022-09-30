<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atom extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'ordinal',
        'molecule_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function molecule()
    {
        return $this->belongsTo(Molecule::class, 'molecule_id');
    }

    public function userAtoms()
    {
        return $this->hasMany(UserAtom::class);
    }
}
