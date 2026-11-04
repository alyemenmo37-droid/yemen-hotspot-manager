<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['name', 'domain'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }
}
