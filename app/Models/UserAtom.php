<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAtom extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'atom_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function atom()
    {
        return $this->belongsTo(Atom::class, 'atom_id');
    }
}
